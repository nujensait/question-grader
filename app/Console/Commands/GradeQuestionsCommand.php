<?php

namespace App\Console\Commands;

use App\Services\QuestionGrader\QuestionGraderService;
use Illuminate\Console\Command;
use Exception;

class GradeQuestionsCommand extends Command
{
    protected $signature = 'grade:questions';

    protected $description = 'Grade exam questions using LLM';

    public function __construct(
        private QuestionGraderService $graderService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $questionsPath = base_path('questions/questions.json');
        $outputPath = base_path('grades.json');

        try {
            $this->graderService->gradeAllQuestions($questionsPath, $outputPath);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Critical error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
