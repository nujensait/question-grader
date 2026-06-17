<?php

namespace App\Services\QuestionGrader;

use App\DTO\QuestionDTO;
use App\Services\Llm\LlmClientInterface;
use App\Services\Llm\GradeCacheInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class QuestionGraderService
{
    private const DEFAULT_GRADE = 1;

    public function __construct(
        private LlmClientInterface $llmClient,
        private GradeCacheInterface $cache,
        private RetryHandler $retryHandler
    ) {}

    public function gradeAllQuestions(string $questionsFilePath, string $outputFilePath): void
    {
        if (!file_exists($questionsFilePath)) {
            throw new Exception("Questions file not found: {$questionsFilePath}");
        }

        $questionsData = json_decode(file_get_contents($questionsFilePath), true);

        if (!is_array($questionsData)) {
            throw new Exception("Invalid questions file format");
        }

        $results = [];

        foreach ($questionsData as $questionData) {
            $question = QuestionDTO::fromArray($questionData);
            $grade = $this->gradeQuestion($question);
            $results[$question->id] = $grade;
        }

        $output = ['results' => $results];
        file_put_contents($outputFilePath, json_encode($output, JSON_PRETTY_PRINT));

        Log::info('Grading completed', [
            'total_questions' => count($results),
            'output_file' => $outputFilePath
        ]);
    }

    private function gradeQuestion(QuestionDTO $question): int
    {
        if ($this->cache->has($question->id)) {
            $cachedGrade = $this->cache->get($question->id);
            Log::debug("Using cached grade for question {$question->id}: {$cachedGrade}");
            return $cachedGrade;
        }

        try {
            $grade = $this->retryHandler->execute(
                fn() => $this->llmClient->gradeQuestion($question)->finalGrade,
                "question {$question->id}"
            );

            $this->cache->set($question->id, $grade);
            Log::info("Successfully graded question {$question->id}: {$grade}");

            return $grade;

        } catch (Exception $e) {
            Log::error("Failed to grade question {$question->id} after all retries", [
                'error' => $e->getMessage()
            ]);

            $this->cache->set($question->id, self::DEFAULT_GRADE);
            return self::DEFAULT_GRADE;
        }
    }
}
