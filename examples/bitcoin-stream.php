<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . "/../vendor/autoload.php";

use BitWasp\Bitcoin\Bitcoin;
use Chikiday\MultiCryptoApi\Blockbook\BitcoinBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Model\IncomingBlock;
use Chikiday\MultiCryptoApi\Model\IncomingTransaction;
use Chikiday\MultiCryptoApi\Stream\BitcoinStream;

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

$stream = new BitcoinStream($blockbook);

ob_implicit_flush(true);
$i = 0;
$block = $stream->subscribeToAnyTransaction(function (IncomingTransaction $tx) use (&$i) {
	$i++;
	$time = date('H:i:s');
	echo "[{$time}] TX {$tx->txid} {$tx->to} +{$tx->amount} BTC [{$i}]\n";
});

$block = $stream->subscribeToAnyBlock(function (IncomingBlock $tx) {
	echo "\n\n\n=====New block\n\n\n {$tx->blockNumber} {$tx->hash} BTC\n\n\n";
	var_dump($tx->txs);
});

$block = $stream->subscribeToAddresses(
	['bc1p2use7h35458lq2zk2xnlpw82mgx9k8fm73nycant9zfcu4gtmk5qg0g7jq'],
	function (IncomingTransaction $tx) {
		echo "\n\n\n======\n\n\n";
		echo "\t\tNEW TRANSACTION\n";
		echo "\t\tTO: {$tx->to}\n";
		echo "\t\tFROM: {$tx->from}\n";
		echo "\t\tAMOUNT: +{$tx->amount}\n";
		echo "\n\n\n======\n\n\n";
	}
);
