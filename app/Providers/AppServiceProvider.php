<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Llm\LlmClientInterface;
use App\Services\Llm\DeepseekClient;
use App\Services\Llm\GradeCacheInterface;
use App\Services\Llm\FileCacheRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LlmClientInterface::class, DeepseekClient::class);
        $this->app->singleton(GradeCacheInterface::class, FileCacheRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
