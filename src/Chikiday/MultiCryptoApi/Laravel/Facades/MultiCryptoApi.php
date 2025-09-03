<?php

namespace Chikiday\MultiCryptoApi\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Chikiday\MultiCryptoApi\Interface\ApiClientInterface chain(string $symbol)
 */
class MultiCryptoApi extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'multicryptoapi';
	}
}
