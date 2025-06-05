<?php

namespace Chikiday\MultiCryptoApi\Interface;


use Chikiday\MultiCryptoApi\Blockchain\Asset;
use Chikiday\MultiCryptoApi\Blockchain\Block;
use Chikiday\MultiCryptoApi\Blockchain\Transaction;

interface BlockchainDataResolver
{
	public function resolveBlock($data): Block;

	public function resolveTx($data): Transaction;

	/**
	 * @param array $data
	 * @return Asset[]
	 */
	public function resolveAssets(array $data): array;

	public function resolveAsset($data): Asset;
}