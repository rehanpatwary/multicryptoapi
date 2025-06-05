<?php

namespace Chikiday\MultiCryptoApi\Blockchain;

readonly class TxvInOut
{
	public function __construct(
		public ?string $address,
		public string|Amount $amount,
		public int $index = 0,
		/** @var Asset[] */
		public array $assets = [],
		public array $originData = [],
	) {
	}
}