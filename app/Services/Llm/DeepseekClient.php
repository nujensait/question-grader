<?php

namespace App\Services\Llm;

use App\DTO\QuestionDTO;
use App\DTO\GradeResultDTO;
use App\Services\QuestionGrader\PromptBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Exception;

class DeepseekClient implements LlmClientInterface
{
    private Client $httpClient;
    private PromptBuilder $promptBuilder;
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct(PromptBuilder $promptBuilder)
    {
        $this->promptBuilder = $promptBuilder;
        $this->apiKey = config('llm.deepseek.api_key');
        $this->apiUrl = config('llm.deepseek.api_url');
        $this->model = config('llm.deepseek.model');

        $this->httpClient = new Client([
            'timeout' => 60,
            'connect_timeout' => 10,
        ]);
    }

    public function gradeQuestion(QuestionDTO $question): GradeResultDTO
    {
        $prompt = $this->promptBuilder->buildPrompt($question);

        try {
            $response = $this->httpClient->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 500,
                    'response_format' => ['type' => 'json_object'],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (!isset($body['choices'][0]['message']['content'])) {
                throw new Exception('Invalid response structure from Deepseek API');
            }

            $content = $body['choices'][0]['message']['content'];
            $gradeData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse JSON from LLM response: ' . json_last_error_msg());
            }

            if (!isset($gradeData['final_grade'])) {
                throw new Exception('Missing final_grade in LLM response');
            }

            $result = GradeResultDTO::fromLlmResponse($gradeData);

            if (!$result->isValid()) {
                throw new Exception("Invalid final_grade value: {$result->finalGrade}");
            }

            return $result;

        } catch (GuzzleException $e) {
            Log::error('Deepseek API request failed', [
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Deepseek API request failed: ' . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            Log::error('Failed to process Deepseek response', [
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
