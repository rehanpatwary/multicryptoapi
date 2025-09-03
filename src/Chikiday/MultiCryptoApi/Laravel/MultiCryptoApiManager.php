<?php

namespace Chikiday\MultiCryptoApi\Laravel;

use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Exception\MultiCryptoApiException;
use Chikiday\MultiCryptoApi\Factory\ApiFactory;
use Chikiday\MultiCryptoApi\Factory\ApiType;
use Chikiday\MultiCryptoApi\Interface\ApiClientInterface;

class MultiCryptoApiManager
{
	public function __construct(
		private readonly array $config = [],
	) {
	}

	/**
	 * Resolve an API client for a given chain symbol (e.g. BTC, ETH, TRX)
	 */
	public function chain(string $symbol): ApiClientInterface
	{
		$symbol = strtoupper(trim($symbol));
		$type = $this->resolveType($symbol);

		$chainConfig = $this->config['chains'][$symbol] ?? null;
		if (!$chainConfig) {
			throw new MultiCryptoApiException("Chain configuration for {$symbol} is missing.");
		}

		$credentials = new RpcCredentials(
			uri: (string)($chainConfig['rpc_uri'] ?? ''),
			blockbookUri: (string)($chainConfig['blockbook_uri'] ?? ''),
			headers: (array)($chainConfig['headers'] ?? []),
			username: $chainConfig['username'] ?? null,
			password: $chainConfig['password'] ?? null,
		);

		return ApiFactory::factory($type, $credentials);
	}

	private function resolveType(string $symbol): ApiType
	{
		return match ($symbol) {
			'BTC' => ApiType::Bitcoin,
			'LTC' => ApiType::Litecoin,
			'DOGE' => ApiType::Dogecoin,
			'DASH' => ApiType::Dash,
			'TRX', 'TRON' => ApiType::Tron,
			'ZEC', 'ZCASH' => ApiType::Zcash,
			'ETH', 'ETHEREUM' => ApiType::Ethereum,
			default => throw new MultiCryptoApiException("Unsupported chain symbol: {$symbol}"),
		};
	}
}
