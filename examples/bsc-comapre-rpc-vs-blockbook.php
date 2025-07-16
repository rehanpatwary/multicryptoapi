<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . "/../vendor/autoload.php";

use Chikiday\MultiCryptoApi\Blockbook\EthereumBlockbook;
use Chikiday\MultiCryptoApi\Blockbook\EthereumRpcBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;

$keys = include_once __DIR__ . '/keys.php';

$rpc = new EthereumRpcBlockbook(
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
	56,
	[
		'tokens' => [
			$contractAddress = '0x55d398326f99059ff775485246999027b3197955',
		],
	]

);


$list = $blockbook->getAddressTransactions('0xcbf593bfb22aa8b4dc561616b2d10dbe0dbe0666');

var_dump($list);
die;

$tx = '0x3bbc76844896b5abbeb5cc152231140ec01c1d740b50f684ab622e28b00fb50f';
$time = microtime(true);
$tx1 = $blockbook->getAddressTransactions($tx);
$time = round(microtime(true) - $time, 5);
echo "Tx (Blockbook) {$tx1->txid} loaded for {$time} seconds.\n";

$time = microtime(true);
$tx2 = $rpc->getTx($tx);
$time = round(microtime(true) - $time, 5);
echo "Tx (RPC) {$tx1->txid} loaded for {$time} seconds.\n";
