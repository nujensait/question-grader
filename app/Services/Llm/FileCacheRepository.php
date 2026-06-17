<?php

namespace App\Services\Llm;

use Illuminate\Support\Facades\Storage;

class FileCacheRepository implements GradeCacheInterface
{
    private const CACHE_FILE = 'grades_cache.json';
    private array $cache = [];
    private bool $loaded = false;

    public function __construct()
    {
        $this->loadCache();
    }

    public function has(string $questionId): bool
    {
        return isset($this->cache[$questionId]);
    }

    public function get(string $questionId): ?int
    {
        return $this->cache[$questionId] ?? null;
    }

    public function set(string $questionId, int $grade): void
    {
        $this->cache[$questionId] = $grade;
        $this->saveCache();
    }

    public function getAll(): array
    {
        return $this->cache;
    }

    private function loadCache(): void
    {
        if ($this->loaded) {
            return;
        }

        if (Storage::exists(self::CACHE_FILE)) {
            $content = Storage::get(self::CACHE_FILE);
            $data = json_decode($content, true);

            if (is_array($data)) {
                $this->cache = $data;
            }
        }

        $this->loaded = true;
    }

    private function saveCache(): void
    {
        Storage::put(self::CACHE_FILE, json_encode($this->cache, JSON_PRETTY_PRINT));
    }
}
