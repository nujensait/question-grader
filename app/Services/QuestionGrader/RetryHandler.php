<?php

namespace App\Services\QuestionGrader;

use Exception;
use Illuminate\Support\Facades\Log;

class RetryHandler
{
    private const MAX_ATTEMPTS = 3;
    private const BASE_DELAY_SECONDS = 1;

    public function execute(callable $callback, string $context = ''): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < self::MAX_ATTEMPTS) {
            $attempt++;

            try {
                return $callback();
            } catch (Exception $e) {
                $lastException = $e;

                Log::warning("Retry attempt {$attempt}/{" . self::MAX_ATTEMPTS . "} failed" .
                    ($context ? " for {$context}" : ''), [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt
                ]);

                if ($attempt < self::MAX_ATTEMPTS) {
                    $delay = self::BASE_DELAY_SECONDS * (2 ** ($attempt - 1));
                    sleep($delay);
                }
            }
        }

        Log::error("All retry attempts exhausted" . ($context ? " for {$context}" : ''), [
            'error' => $lastException?->getMessage()
        ]);

        throw $lastException ?? new Exception('Unknown error during retry execution');
    }
}
