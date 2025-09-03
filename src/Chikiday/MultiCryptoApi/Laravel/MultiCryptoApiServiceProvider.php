<?php

namespace Chikiday\MultiCryptoApi\Laravel;

use Illuminate\Support\ServiceProvider;

class MultiCryptoApiServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/config/multicryptoapi.php', 'multicryptoapi');

		$this->app->singleton('multicryptoapi', function ($app) {
			$config = $app['config']->get('multicryptoapi', []);
			return new MultiCryptoApiManager($config);
		});
	}

	public function boot(): void
	{
		$this->publishes([
			__DIR__ . '/config/multicryptoapi.php' => config_path('multicryptoapi.php'),
		], 'multicryptoapi-config');
	}
}
