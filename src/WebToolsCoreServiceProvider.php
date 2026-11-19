<?php

declare(strict_types=1);

namespace Yu\WebToolsCore;

use Illuminate\Support\ServiceProvider;

/**
 * Shared parts of the web tools: safe page loading, locale picking, the checklist report and its view.
 */
class WebToolsCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/web-tools-core.php', 'web-tools-core');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'web-tools-core');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'web-tools-core');
    }
}
