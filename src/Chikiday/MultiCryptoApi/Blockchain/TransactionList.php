<?php

namespace Chikiday\MultiCryptoApi\Blockchain;

readonly class TransactionList
{
	public function __construct(
		/** @var Transaction[] */
		public array $transactions,
		public int $page,
		public int $totalPages,
		public array $originData = [],
	) {
	}
}