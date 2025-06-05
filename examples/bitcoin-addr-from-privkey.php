<?php

require_once __DIR__ . "/../vendor/autoload.php";

use BitWasp\Bitcoin\Bitcoin;
use Chikiday\MultiCryptoApi\Blockbook\BitcoinBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;

$keys = include_once __DIR__ . '/keys.php';

$blockbook = new BitcoinBlockbook(
	new RpcCredentials(
		'https://btc.nownodes.io',
		'https://btcbook.nownodes.io',
		[
			'api-key' => $keys['NowNodes'],
		]
	),
	Bitcoin::getNetwork(),
);

$api = new \Chikiday\MultiCryptoApi\Api\BitcoinApiClient($blockbook);

$wallet = $api->createWallet();
echo "Wallet {$wallet->address}\n";

$wallet2 = $api->createFromPrivateKey($wallet->privateKey);
echo "Wallet form privkey {$wallet2->address}\n";