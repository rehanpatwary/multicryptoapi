<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . "/../vendor/autoload.php";

use Chikiday\MultiCryptoApi\Blockbook\TrxBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Model\IncomingTransaction;
use Chikiday\MultiCryptoApi\Stream\TronStream;
use Chikiday\MultiCryptoApi\Stream\Logger\FileLogger;
use Chikiday\MultiCryptoApi\Stream\TrxStream;

$keys = include_once __DIR__ . '/keys.php';

$blockbook = new TrxBlockbook(
	new RpcCredentials(
		'https://trx.nownodes.io',
		'https://trx-blockbook.nownodes.io',
		[
			'api-key' => $keys['NowNodes'], // for now nodes
			'TRON-PRO-API-KEY' => $keys['TronScan'], // for tronscan
		]
	),
	$keys['TronGridApiKey']
);
$stream = new TronStream($blockbook);


$logger = new \Chikiday\MultiCryptoApi\Log\StdoutLogger();
$stream->setLogger($logger);

$stream->subscribeToAnyTransaction(function (IncomingTransaction $tx) {

});