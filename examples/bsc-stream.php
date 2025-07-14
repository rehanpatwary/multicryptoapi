<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . "/../vendor/autoload.php";

use Chikiday\MultiCryptoApi\Blockbook\EthereumBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Log\StdoutLogger;
use Chikiday\MultiCryptoApi\Model\IncomingBlock;
use Chikiday\MultiCryptoApi\Model\IncomingTransaction;
use Chikiday\MultiCryptoApi\Stream\EthereumStream;

$keys = include_once __DIR__ . '/keys.php';

$blockbook = new EthereumBlockbook(
	new RpcCredentials(
		'https://bsc.nownodes.io/' . $keys['NowNodes'],
		'https://bsc-blockbook.nownodes.io',
		[
			'api-key' => $keys['NowNodes']
		]
	),
//	'wss://bsc-mainnet.infura.io/ws/v3/' . $keys['Infura'],
	null,
	56
);

$blockbook->debug = true;


class StringCounter
{
	private array $counts = [];

	public function percent(string $input): float
	{
		$this->counts[$input] ??= 0;
		$this->counts[$input]++;

		return ($this->counts[$input] / array_sum($this->counts)) * 100;
	}
}

$counter = new StringCounter();

$eth = new EthereumStream($blockbook);
$logger = new StdoutLogger();
$eth->setLogger($logger);

$eth->debug = false;

$time = time();
$blocks = 0;

$eth->subscribeToAnyBlock(function (IncomingBlock $block) use(&$blocks, $time) {
	$blocks ++ ;
	$_time = time() - $time;
	echo "New block {$block->blockNumber} mined, " . count($block->txs) . " txs [{$blocks} blocks | {$_time} s.]\n";

	foreach ($block->txs as $tx) {
//		echo "Tx {$tx->txid} {$tx->from} -> {$tx->to} {$tx->amount} {$tx->contractAddress}\n";
	}
});

$i = 0;
$eth->subscribeToAnyTransaction(function (IncomingTransaction $tx, bool $fromBlock = false) use (&$i, $counter) {
	$i++;
	$percent = round($counter->percent($tx->to), 5);
//	echo "Tx {$tx->txid}:{$tx->index} {$tx->from} -> {$tx->to} {$tx->amount} {$tx->contractAddress} [{$percent}]";
//	echo($fromBlock ? " [FROM BLOCK]" : "");
//	echo "\n";
});