<?php

namespace Chikiday\MultiCryptoApi\Laravel;

use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Factory\ApiFactory;
use Chikiday\MultiCryptoApi\Factory\ApiType;
use Chikiday\MultiCryptoApi\Interface\ApiClientInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use InvalidArgumentException;

class MultiCryptoApiManager
{
    protected ?ConfigRepository $config;
    protected array $drivers = [];

    public function __construct(?ConfigRepository $config = null)
    {
        $this->config = $config;
    }

    /**
     * Resolve an API client for a given chain symbol, e.g. BTC, ETH, TRX.
     */
    public function chain(string $symbol): ApiClientInterface
    {
        $key = strtoupper($symbol);

        if (!isset($this->drivers[$key])) {
            $this->drivers[$key] = $this->resolve($key);
        }

        return $this->drivers[$key];
    }

    /**
     * Proxy calls to the default chain client.
     * Example: MultiCryptoApi::createWallet() -> uses default chain.
     */
    public function __call(string $method, array $arguments)
    {
        $client = $this->default();

        return $client->{$method}(...$arguments);
    }

    /**
     * Get the default chain client as configured.
     */
    public function default(): ApiClientInterface
    {
        $symbol = $this->getConfigValue('multicryptoapi.default', 'BTC');

        return $this->chain((string)$symbol);
    }

    protected function resolve(string $symbol): ApiClientInterface
    {
        $cfg = $this->getChainConfig($symbol);

        if (!$cfg) {
            throw new InvalidArgumentException("MultiCryptoApi: chain {$symbol} is not configured.");
        }

        $uri = $cfg['uri'] ?? null;
        $blockbookUri = $cfg['blockbook_uri'] ?? null;
        if (!$uri || !$blockbookUri) {
            throw new InvalidArgumentException("MultiCryptoApi: chain {$symbol} requires 'uri' and 'blockbook_uri' in config.");
        }

        $credentials = new RpcCredentials(
            $uri,
            $blockbookUri,
            (array)($cfg['headers'] ?? []),
            $cfg['username'] ?? null,
            $cfg['password'] ?? null,
        );

        $type = $this->mapSymbolToType($symbol);

        return ApiFactory::factory($type, $credentials);
    }

    protected function getChainConfig(string $symbol): ?array
    {
        $chains = (array)$this->getConfigValue('multicryptoapi.chains', []);

        return $chains[strtoupper($symbol)] ?? null;
    }

    protected function getConfigValue(string $key, mixed $default = null): mixed
    {
        // Prefer injected config repository; fall back to global helper if available.
        if ($this->config) {
            return $this->config->get($key, $default);
        }

        if (function_exists('config')) {
            return config($key, $default);
        }

        return $default;
    }

    protected function mapSymbolToType(string $symbol): ApiType
    {
        return match (strtoupper($symbol)) {
            'BTC' => ApiType::Bitcoin,
            'LTC' => ApiType::Litecoin,
            'DOGE' => ApiType::Dogecoin,
            'DASH' => ApiType::Dash,
            'TRX', 'TRON' => ApiType::Tron,
            'ZEC' => ApiType::Zcash,
            'ETH', 'ETHEREUM' => ApiType::Ethereum,
            default => throw new InvalidArgumentException("Unsupported chain symbol: {$symbol}"),
        };
    }
}
