<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . "/../vendor/autoload.php";

use Chikiday\MultiCryptoApi\Blockbook\EthereumBlockbook;
use Chikiday\MultiCryptoApi\Blockbook\EthereumRpc;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;

$keys = include_once __DIR__ . '/keys.php';

$rpc = new EthereumRpc(
	new RpcCredentials(
		'https://bsc.nownodes.io/' . $keys['NowNodes'],
		'https://bsc-blockbook.nownodes.io',
		[
			'api-key' => $keys['NowNodes'],
		]
	),
	null,
	56,
	[
		'tokens' => [
			$contractAddress = '0x55d398326f99059ff775485246999027b3197955',
		],
		'etherscanApiKey' => $keys['Etherscan'],
	]
);


$blockbook = new EthereumBlockbook(
	new RpcCredentials(
		'https://bsc.nownodes.io/' . $keys['NowNodes'],
		'https://bsc-blockbook.nownodes.io',
		[
			'api-key' => $keys['NowNodes'],
		]
	),
	null,
	56
);


try {
	$time = microtime(true);
	$list = $rpc->getAddressTransactions('0xcbF593BfB22aa8B4dc561616b2D10dbe0DbE0666');
	$time = round(microtime(true) - $time, 5);
	echo "Loaded ". count($list->transactions) ." txs from RPC api, for {$time} seconds.\n";

	foreach ($list->transactions as $transaction) {
		echo "TX {$transaction->txid}\n";
	}
} catch (\Exception $e) {
	echo "Cant load txs by RPC: {$e->getMessage()}\n";
}

try {
	$time = microtime(true);
	$list = $blockbook->getAddressTransactions('0xcbF593BfB22aa8B4dc561616b2D10dbe0DbE0666');
	$time = round(microtime(true) - $time, 5);
	echo "Loaded ". count($list->transactions) ." txs from Blockbook api, for {$time} seconds.\n";
	foreach ($list->transactions as $transaction) {
		echo "TX {$transaction->txid}\n";
	}
} catch (\Exception $e) {
	echo "Cant load txs by Blockbook: {$e->getMessage()}\n";
}