<?php

namespace App\Services\Llm;

use App\DTO\QuestionDTO;
use App\DTO\GradeResultDTO;

interface LlmClientInterface
{
    public function gradeQuestion(QuestionDTO $question): GradeResultDTO;
}
