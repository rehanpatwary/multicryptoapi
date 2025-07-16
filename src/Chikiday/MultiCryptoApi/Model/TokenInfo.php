<?php

namespace Chikiday\MultiCryptoApi\Model;

use Chikiday\MultiCryptoApi\Blockchain\Amount;
use Chikiday\MultiCryptoApi\Blockchain\Asset;

readonly class TokenInfo implements \JsonSerializable
{
	public function __construct(
		public string $contract,
		public string $name,
		public string $symbol,
		public int    $decimals,
		public string $type,
	)
	{
	}

	public static function import(array $data): self
	{
		return new self(
			contract: $data['contract'],
			name: $data['name'],
			symbol: $data['symbol'],
			decimals: $data['decimals'],
			type: $data['type'] ?? 'erc20'
		);
	}

	// add import method

	public function jsonSerialize(): mixed
	{
		return [
			'contract' => $this->contract,
			'name'     => $this->name,
			'symbol'   => $this->symbol,
			'decimals' => $this->decimals,
			'type'     => $this->type,
		];
	}

	public function toAsset(string $amount): Asset
	{
		return new Asset(
			$this->type,
			$this->contract,
			Amount::satoshi($amount, $this->decimals),
			$this->name,
			$this->symbol,
		);
	}
}