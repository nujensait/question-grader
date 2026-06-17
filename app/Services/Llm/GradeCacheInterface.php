<?php

namespace App\Services\Llm;

interface GradeCacheInterface
{
    public function has(string $questionId): bool;

    public function get(string $questionId): ?int;

    public function set(string $questionId, int $grade): void;

    public function getAll(): array;
}
