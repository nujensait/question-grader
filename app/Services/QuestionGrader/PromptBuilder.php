<?php

namespace App\Services\QuestionGrader;

use App\DTO\QuestionDTO;

class PromptBuilder
{
    public function buildPrompt(QuestionDTO $question): string
    {
        $distractors = implode("\n", array_map(
            fn($d, $i) => ($i + 1) . ". " . $d,
            $question->distractors,
            array_keys($question->distractors)
        ));

        return <<<PROMPT
You are an expert exam question evaluator. Evaluate the following question based on 4 criteria and return ONLY valid JSON.

Question: {$question->question}

Correct Answer: {$question->answer}

Distractors (wrong answers):
{$distractors}

Explanation: {$question->explanation}

Evaluation Criteria (rate each 1-10):

1. Content Validity: Does the question test important, meaningful knowledge rather than trivial facts?
2. Construct Clarity: Is the question clear, unambiguous, and well-formulated?
3. Distractor Quality: Are the wrong answers plausible, uniform, and not obviously incorrect? Are there no multiple correct answers?
4. Explanation Usefulness: Does the explanation clearly explain why the answer is correct and why distractors are wrong, rather than just rephrasing the question?

Return ONLY this JSON structure with no additional text:
{
  "content_validity": <integer 1-10>,
  "construct_clarity": <integer 1-10>,
  "distractor_quality": <integer 1-10>,
  "explanation_quality": <integer 1-10>,
  "final_grade": <integer 1-10>
}

The final_grade should be calculated based on the four criteria scores.
PROMPT;
    }
}
