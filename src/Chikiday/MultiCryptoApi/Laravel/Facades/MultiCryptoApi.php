<?php

namespace Chikiday\MultiCryptoApi\Laravel\Facades;

use Chikiday\MultiCryptoApi\Interface\ApiClientInterface;
use Chikiday\MultiCryptoApi\Laravel\MultiCryptoApiManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ApiClientInterface chain(string $symbol)
 * @method static ApiClientInterface default()
 *
 * @see MultiCryptoApiManager
 */
class MultiCryptoApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'multicryptoapi';
    }
}
