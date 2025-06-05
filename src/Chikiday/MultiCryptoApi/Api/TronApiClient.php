<?php

namespace Chikiday\MultiCryptoApi\Api;

use Chikiday\MultiCryptoApi\Blockbook\TrxBlockbook;
use Chikiday\MultiCryptoApi\Blockchain\Address as BlockchainAddress;
use Chikiday\MultiCryptoApi\Blockchain\AddressCredentials;
use Chikiday\MultiCryptoApi\Blockchain\Fee;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;
use Chikiday\MultiCryptoApi\Blockchain\TxvInOut;
use Chikiday\MultiCryptoApi\Exception\IncorrectTxException;
use Chikiday\MultiCryptoApi\Interface\AddressActivator;
use Chikiday\MultiCryptoApi\Interface\ApiClientInterface;
use Chikiday\MultiCryptoApi\Interface\BlockbookInterface;
use Chikiday\MultiCryptoApi\Interface\ResourceRentableInterface;
use Chikiday\MultiCryptoApi\Interface\StreamableInterface;
use Chikiday\MultiCryptoApi\Stream\TronStream;
use IEXBase\TronAPI\Exception\TronException;
use InvalidArgumentException;
use Override;
use Tron\Address;
use Tron\Exceptions\TronErrorException;
use Tron\Support\Key;

class TronApiClient implements ApiClientInterface, ResourceRentableInterface, AddressActivator
{
	private TronStream $stream;

	public function __construct(
		private readonly TrxBlockbook $blockbook,
	)
	{
	}

	#[Override] public function stream(): ?StreamableInterface
	{
		if (!isset($this->stream)) {
			$this->stream = new TronStream($this->blockbook);
		}
		return $this->stream;
	}

	public function createWallet(): AddressCredentials
	{
		$generateAddress = $this->blockbook->tron->generateAddress();

		return new AddressCredentials(
			$generateAddress->getAddress(true),
			$generateAddress->getPrivateKey(),
			[
				'raw' => $generateAddress->getRawData(),
			]
		);
	}

	#[Override] public function createFromPrivateKey(string $privateKey): AddressCredentials
	{
		try {
			$addressHex = Address::ADDRESS_PREFIX . Key::privateKeyToAddress($privateKey);
			$addressBase58 = Key::getBase58CheckAddress($addressHex);
		} catch (InvalidArgumentException $e) {
			throw new TronErrorException($e->getMessage());
		}
		$address = new Address($addressBase58, $privateKey, $addressHex);
		$validAddress = $this->blockbook->tron->validateAddress($address->address);
		if (!$validAddress) {
			throw new TronErrorException('Invalid private key');
		}

		return new AddressCredentials(
			$addressBase58,
			$privateKey
		);
	}

	public function sendCoins(
		AddressCredentials $from,
		string $addressTo,
		string $amount,
		?Fee   $fee = null,
	): Transaction
	{
		$tron = $this->blockbook->tron;
		$tron->setAddress($from->address);
		$tron->setPrivateKey($from->privateKey);

		try {
			$transfer = $tron->sendTransaction($addressTo, $amount);
		} catch (TronException $e) {
			throw new IncorrectTxException($e->getMessage(), $e->getCode(), $e);
		}

		$vin = new TxvInOut($from->address, $amount, 0);
		$vout = new TxvInOut($addressTo, $amount, 0);

		$isSuccess = $transfer['result'] ?? false;
		if (!$isSuccess) {
			$error = $transfer['code'] . ": " . $tron->fromHex($transfer['message']);
		}

		return new Transaction(
			$transfer['txid'],
			0,
			0,
			time(),
			[$vin],
			[$vout],
			"0",
			$transfer,
			$isSuccess,
			$error ?? null
		);
	}

	public function sendAsset(
		AddressCredentials $from,
		string $assetId,
		string $addressTo,
		string $amount,
		?int   $decimals = null,
		?Fee   $fee = null,
	): Transaction
	{
		$tron = $this->blockbook->tron;
		$tron->setPrivateKey($from->privateKey);
		$contract = $tron->contract($assetId);

		if ($fee?->gasLimit) {
			$contract->setFeeLimit((int) $fee->gasLimit->toBtc());
		}

		try {
			$transfer = $contract->transfer($addressTo, $amount, $from->address);
		} catch (TronException $e) {
			throw new IncorrectTxException($e->getMessage(), $e->getCode(), $e);
		}

		$isSuccess = (bool) ($transfer['result'] ?? false);

		$tx = new Transaction(
			$transfer['txid'],
			0,
			0,
			time(),
			[],
			[],
			$fee,
			$transfer,
			$isSuccess,
			$transfer['code'] ?? null
		);

		return $tx;
	}

	#[Override] public function blockbook(): BlockbookInterface
	{
		return $this->blockbook;
	}

	public function delegateResource(
		AddressCredentials $from,
		string $type,
		string $addressTo,
		int    $amount,
	): Transaction
	{
		$tron = $this->blockbook->tron;
		$tron->setPrivateKey($from->privateKey);

		try {
			$unsignedTx = $this->blockbook->tron->getManager()->request('/wallet/delegateresource', [
				'owner_address'    => $from->address,
				'receiver_address' => $addressTo,
				'balance'          => $this->blockbook()->tron->toTron($amount),
				'resource'         => strtoupper($type),
				'lock'             => false,
				'visible'          => true,
			]);
			if (isset($unsignedTx['Error'])) {
				[, $error] = explode(' : ', $unsignedTx['Error']);
				throw new TronErrorException($error);
			}

			$signedTx = $tron->signTransaction($unsignedTx);
			$response = $tron->sendRawTransaction($signedTx);
			$signedTx = array_merge($response, $signedTx);
		} catch (TronException $e) {
			throw new IncorrectTxException($e->getMessage(), $e->getCode(), $e);
		}

		return new Transaction($signedTx['txid'], null, 0, time(), [], [], 0, $signedTx);
	}

	public function cancelDelegateResource(
		AddressCredentials $from,
		string $type,
		string $addressTo,
		int    $amount,
	): Transaction
	{
		$tron = $this->blockbook->tron;
		$tron->setPrivateKey($from->privateKey);

		try {
			$unsignedTx = $this->blockbook->tron->getManager()->request('/wallet/undelegateresource', [
				'owner_address'    => $from->address,
				'receiver_address' => $addressTo,
				'balance'          => $amount,
				'resource'         => strtoupper($type),
				'visible'          => true,
			]);
			if (isset($unsignedTx['Error'])) {
				[, $error] = explode(' : ', $unsignedTx['Error']);
				throw new TronErrorException($error);
			}

			$signedTx = $tron->signTransaction($unsignedTx);
			$response = $tron->sendRawTransaction($signedTx);
			$signedTx = array_merge($response, $signedTx);
		} catch (TronException $e) {
			throw new IncorrectTxException($e->getMessage(), $e->getCode(), $e);
		}

		return new Transaction($signedTx['txid'], null, 0, time(), [], [], 0, $signedTx);
	}

	public function getResourcePrices(string $address): array
	{
		$response = $this->blockbook->tron->getManager()->request("/wallet/getaccountresource", [
			'address' => $address,
			'visible' => true,
		]);

		return [
			round($response['TotalEnergyLimit'] / ($response['TotalEnergyWeight'] ?: 1), 5),
			round($response['TotalNetLimit'] / ($response['TotalNetWeight'] ?: 1), 5),
		];
	}

	public function activateAddress(
		AddressCredentials $from,
		string             $address,
	): Transaction
	{
		$tron = $this->blockbook->tron;
		$tron->setPrivateKey($from->privateKey);

		try {
			$unsignedTx = $this->blockbook->tron->getManager()->request('/wallet/createaccount', [
				'owner_address'   => $from->address,
				'account_address' => $address,
				'visible'         => true,
			]);
			if (isset($unsignedTx['Error'])) {
				[, $error] = explode(' : ', $unsignedTx['Error']);
				throw new TronErrorException($error);
			}

			$signedTx = $tron->signTransaction($unsignedTx);
			$response = $tron->sendRawTransaction($signedTx);
			$signedTx = array_merge($response, $signedTx);
		} catch (TronException $e) {
			throw new IncorrectTxException($e->getMessage(), $e->getCode(), $e);
		}

		return new Transaction($signedTx['txid'], null, 0, time(), [], [], 0, $signedTx);
	}

	public function isAddressActive(string|BlockchainAddress $address): bool
	{
		if (!$address instanceof BlockchainAddress) {
			$address = $this->blockbook->getAddress($address);
		}

		return $address->originData['details']['isActive'];
	}

	public function validateAddress(string $address): bool
	{
		return $this->blockbook->tron->isAddress($address);
	}
}