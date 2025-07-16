<?php

namespace Chikiday\MultiCryptoApi\Blockbook;


use Chikiday\MultiCryptoApi\Blockchain\Address;
use Chikiday\MultiCryptoApi\Blockchain\Amount;
use Chikiday\MultiCryptoApi\Blockchain\Asset;
use Chikiday\MultiCryptoApi\Blockchain\Block;
use Chikiday\MultiCryptoApi\Blockchain\RpcCredentials;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;
use Chikiday\MultiCryptoApi\Blockchain\TransactionList;
use Chikiday\MultiCryptoApi\Blockchain\TxvInOut;
use Chikiday\MultiCryptoApi\Model\TokenInfo;
use Chikiday\MultiCryptoApi\Util\EthUtil;
use Override;

/**
 * @property  ?RpcCredentials $credentials
 */
class EthereumRpcBlockbook extends EthereumBlockbook
{
	protected ?array $allowedTokens = null;

	#[Override] public function getAddressTransactions(
		string $address,
		int    $page = 1,
		int    $pageSize = 1000,
	): TransactionList
	{
	}

	#[Override] public function getBlock(string $hash = 'latest'): Block
	{
		if ($hash == 'latest' || is_numeric($hash)) {
			if (is_numeric($hash)) {
				$hash = "0x" . dechex($hash);
			}
			$data = $this->jsonRpc('eth_getBlockByNumber', [$hash, true]);
		} else {
			$data = $this->jsonRpc('eth_getBlockByHash', [$hash, true]);
		}

		return new Block(
			hexdec($data['number']),
			$data['hash'],
			array_column($data['transactions'] ?? [], 'hash'),
			$data['parentHash'],
			$data,
		);
	}

	#[Override] public function getTx(string $txid): ?Transaction
	{
		$data = $this->jsonRpcWithRetry('eth_getTransactionByHash', [$txid]);

		$util = new EthUtil();

		$outputs = $inputs = [];
		$receipt = $this->jsonRpcWithRetry('eth_getTransactionReceipt', [$txid]);
		$success = hexdec($receipt['status'] ?? "0x") > 0;

		$token = $this->getAllowedTokens()[$data['to'] ?? ''] ?? null;
		if ($token) {
			if (!$success) {
				$error = "unsuccessful";
			}

			foreach ($receipt['logs'] ?? [] as $log) {
				if (!$transferByLog = $util->getTransferByLog($token, $log)) {
					continue;
				}

				$outputs[] = new TxvInOut(
					$transferByLog->to,
					"0",
					hexdec($data['transactionIndex']),
					$_assets = [
						$token->toAsset($transferByLog->value),
					],
					$log
				);

				$inputs[] = new TxvInOut(
					$transferByLog->from,
					"0",
					hexdec($data['transactionIndex']),
					$_assets,
					$log
				);
			}
		} else {
			$value = $util->hexToDec($data['value']);

			$outputs[] = new TxvInOut(
				$data['from'],
				$value,
				hexdec($data['transactionIndex']),
				[],
				$data
			);

			$inputs[] = new TxvInOut(
				$data['to'],
				$value,
				hexdec($data['transactionIndex']),
				[],
				$data
			);
		}

		return new Transaction(
			$txid,
			hexdec($data['blockNumber']),
			0,
			0,
			$inputs,
			$outputs,
			0,
			['tx' => $data, 'receipt' => $receipt ?? null],
			$success,
			$error ?? null
		);
	}


	#[Override] public function getAddress(string $address, bool $loadAssets = false): Address
	{
		$data = $this->jsonRpc('eth_getBalance', [$address, 'latest']);

		$balance = EthUtil::hexToDec($data ?? "0x");
		$balance = Amount::satoshi($balance, $this->getDecimals());

		$assets = $loadAssets ? $this->getAssets($address) : [];

		return new Address($address, $balance, $assets);
	}

	#[Override] public function getAssets(string $address): array
	{
		$assets = [];
		foreach ($this->getAllowedTokens() as $asset) {
			if (!$balance = $this->getErc20Balance($asset->contract, $address)) {
				continue;
			}
			$assets[$asset->contract] = new Asset(
				$asset->type,
				$asset->contract,
				$balance,
				$asset->name,
				$asset->symbol
			);
		}

		return $assets;
	}

	#[Override] public function getUTXO(string $address, bool $confirmed = true): array
	{
		return [];
	}

	public function getDecimals(): int
	{
		return 18;
	}

	public function getName(): string
	{
		return "BNB";
	}

	public function getSymbol(): string
	{
		return "BNB";
	}

	/**
	 * @return TokenInfo[]
	 */
	protected function getAllowedTokens(): array
	{
		if (isset($this->allowedTokens)) {
			return $this->allowedTokens;
		}

		$tokens = $this->getOption('tokens') ?? [];
		$_assets = [];
		foreach ($tokens as $token) {
			$info = $this->getTokenInfo($token);
			$_assets[$token] = $info;
		}

		return $this->allowedTokens = $_assets;
	}
}