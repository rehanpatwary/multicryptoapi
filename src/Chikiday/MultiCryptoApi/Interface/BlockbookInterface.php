<?php

namespace Chikiday\MultiCryptoApi\Interface;


use Chikiday\MultiCryptoApi\Blockchain\Address;
use Chikiday\MultiCryptoApi\Blockchain\Asset;
use Chikiday\MultiCryptoApi\Blockchain\Block;
use Chikiday\MultiCryptoApi\Blockchain\PushedTX;
use Chikiday\MultiCryptoApi\Blockchain\RawTransaction;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;
use Chikiday\MultiCryptoApi\Blockchain\TransactionList;
use Chikiday\MultiCryptoApi\Blockchain\UTXO;

interface BlockbookInterface
{
	public function getOption(string $key): mixed;

	public function getDecimals(): int;

	public function getName(): string;

	public function getSymbol(): string;

	public function getBlock(string $hash = 'latest'): Block;

	public function getTx(string $txId): ?Transaction;

	public function getAddress(string $address): Address;

	public function getAddressTransactions(string $address, int $page = 1, int $pageSize = 1000): TransactionList;

	/**
	 * @param string $address
	 * @return Asset[]
	 */
	public function getAssets(string $address): array;

	public function pushRawTransaction(RawTransaction $hex): PushedTX;

	/**
	 * @param string $address
	 * @param bool $confirmed
	 * @return UTXO[]
	 */
	public function getUTXO(string $address, bool $confirmed = true): array;
}