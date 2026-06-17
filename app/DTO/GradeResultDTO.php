<?php

namespace App\DTO;

class GradeResultDTO
{
    public function __construct(
        public readonly int $contentValidity,
        public readonly int $constructClarity,
        public readonly int $distractorQuality,
        public readonly int $explanationQuality,
        public readonly int $finalGrade
    ) {}

    public static function fromLlmResponse(array $response): self
    {
        return new self(
            contentValidity: $response['content_validity'] ?? 1,
            constructClarity: $response['construct_clarity'] ?? 1,
            distractorQuality: $response['distractor_quality'] ?? 1,
            explanationQuality: $response['explanation_quality'] ?? 1,
            finalGrade: $response['final_grade'] ?? 1
        );
    }

    public function isValid(): bool
    {
        return $this->finalGrade >= 1 && $this->finalGrade <= 10;
    }
}
