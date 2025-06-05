<?php

namespace Chikiday\MultiCryptoApi\Interface;


use Chikiday\MultiCryptoApi\Blockchain\AddressCredentials;
use Chikiday\MultiCryptoApi\Blockchain\Fee;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;

interface ApiClientInterface
{
	public function blockbook(): BlockbookInterface;

	public function stream(): ?StreamableInterface;

	public function createWallet(): AddressCredentials;

	public function createFromPrivateKey(string $privateKey): AddressCredentials;

	public function sendCoins(
		AddressCredentials $from,
		string $addressTo,
		string $amount,
		?Fee $fee = null,
	): Transaction;

	public function sendAsset(
		AddressCredentials $from,
		string $assetId,
		string $addressTo,
		string $amount,
		?int $decimals = null,
		?Fee $fee = null,
	): Transaction;

	public function validateAddress(string $address): bool;
}