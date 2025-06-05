<?php

namespace Chikiday\MultiCryptoApi\Factory;

use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Network\NetworkInterface;
use Chikiday\MultiCryptoApi\Api\BitcoinApiClient;
use Chikiday\MultiCryptoApi\Api\EthereumApiClient;
use Chikiday\MultiCryptoApi\Api\TronApiClient;
use Chikiday\MultiCryptoApi\Blockbook\BitcoinBlockbook;
use Chikiday\MultiCryptoApi\Blockbook\EthereumBlockbook;
use Chikiday\MultiCryptoApi\Blockbook\TrxBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Interface\ApiClientInterface;

class ApiFactory
{
	public static function factory(ApiType $type, RpcCredentials $credentials): ApiClientInterface
	{
		return match ($type) {
			ApiType::Bitcoin => self::bitcoin($credentials),
			ApiType::Litecoin => self::litecoin($credentials),
			ApiType::Dogecoin => self::doge($credentials),
			ApiType::Dash => self::dash($credentials),
			ApiType::Tron => self::tron($credentials),
			ApiType::Zcash => self::zcash($credentials),
			ApiType::Ethereum => self::ethereum($credentials),
		};
	}

	public static function bitcoin(RpcCredentials $credentials): BitcoinApiClient
	{
		return self::bitcoinLike(NetworkFactory::bitcoin(), $credentials);
	}

	private static function bitcoinLike(NetworkInterface $network, RpcCredentials $credentials): BitcoinApiClient
	{
		return new BitcoinApiClient(
			new BitcoinBlockbook($credentials, $network)
		);
	}

	public static function litecoin(RpcCredentials $credentials): BitcoinApiClient
	{
		return self::bitcoinLike(NetworkFactory::litecoin(), $credentials);
	}

	public static function doge(RpcCredentials $credentials): BitcoinApiClient
	{
		return self::bitcoinLike(NetworkFactory::dogecoin(), $credentials);
	}

	public static function dash(RpcCredentials $credentials): BitcoinApiClient
	{
		return self::bitcoinLike(NetworkFactory::dash(), $credentials);
	}

	public static function tron(RpcCredentials $credentials): TronApiClient
	{
		return new TronApiClient(new TrxBlockbook($credentials));
	}

	public static function zcash(RpcCredentials $credentials): BitcoinApiClient
	{
		return self::bitcoinLike(NetworkFactory::zcash(), $credentials);
	}

	public static function ethereum(RpcCredentials $credentials): EthereumApiClient
	{
		return new EthereumApiClient(new EthereumBlockbook($credentials));
	}
}