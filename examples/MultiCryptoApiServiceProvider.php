<?php

namespace Chikiday\MultiCryptoApi\Laravel;

use Illuminate\Support\ServiceProvider;

class MultiCryptoApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom($this->packageConfigPath(), 'multicryptoapi');

        // Bind as a singleton in the service container
        $this->app->singleton('multicryptoapi', function ($app) {
            return new MultiCryptoApiManager($app['config'] ?? null);
        });

        // Also bind by class name for type-hinting
        $this->app->alias('multicryptoapi', MultiCryptoApiManager::class);
    }

    public function boot(): void
    {
        // Allow publishing the config to the application's config path
        $this->publishes([
            $this->packageConfigPath() => $this->app->configPath('multicryptoapi.php'),
        ], 'config');
    }

    protected function packageConfigPath(): string
    {
        // This file lives at <package-root>/config/multicryptoapi.php
        // Provider is at <package-root>/src/Chikiday/MultiCryptoApi/Laravel
        return dirname(__DIR__, 4) . '/config/multicryptoapi.php';
    }

    public function provides(): array
    {
        return ['multicryptoapi', MultiCryptoApiManager::class];
    }
}
