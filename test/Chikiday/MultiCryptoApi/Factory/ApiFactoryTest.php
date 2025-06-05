<?php

namespace Chikiday\MultiCryptoApi\Factory;

use Chikiday\MultiCryptoApi\Api\TronApiClient;
use Chikiday\MultiCryptoApi\Blockbook\TrxBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use PHPUnit\Framework\TestCase;

class ApiFactoryTest extends TestCase
{
	public function testTronFactory()
	{
		$api = ApiFactory::tron(new RpcCredentials('', ''));

		$this->assertInstanceOf(TronApiClient::class, $api);
		$this->assertInstanceOf(TrxBlockbook::class, $api->blockbook());
	}
}
