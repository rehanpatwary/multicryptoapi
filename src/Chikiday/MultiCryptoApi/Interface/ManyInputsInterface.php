<?php

namespace Chikiday\MultiCryptoApi\Interface;


use Chikiday\MultiCryptoApi\Blockchain\AddressCredentials;
use Chikiday\MultiCryptoApi\Blockchain\Fee;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;
use Chikiday\MultiCryptoApi\Blockchain\TxOutput;

interface ManyInputsInterface
{

	/**
	 * @param AddressCredentials[] $from
	 * @param TxOutput[] $to
	 * @param Fee|null $fee
	 * @return Transaction
	 */
	public function sendMany(array $from, array $to, ?Fee $fee = null): Transaction;

}