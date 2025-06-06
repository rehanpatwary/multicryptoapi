<?php

namespace Chikiday\MultiCryptoApi\Interface;


use Chikiday\MultiCryptoApi\Blockchain\Address;
use Chikiday\MultiCryptoApi\Blockchain\AddressCredentials;
use Chikiday\MultiCryptoApi\Blockchain\Amount;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;

/**
 * Interface for networks that require an address to be activated (like Tron)
 */
interface AddressActivator
{
	public function activateAddress(
		AddressCredentials $from,
		string             $address,
	): Transaction;

	public function isAddressActive(Address|string $address): bool;

	public function getActivationFee(): Amount;
}