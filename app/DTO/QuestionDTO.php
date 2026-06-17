<?php

namespace App\DTO;

class QuestionDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $question,
        public readonly string $answer,
        public readonly array $distractors,
        public readonly string $explanation
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            question: $data['question'],
            answer: $data['answer'],
            distractors: $data['distractors'],
            explanation: $data['explanation']
        );
    }
}
