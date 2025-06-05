<?php

namespace Chikiday\MultiCryptoApi\Model;

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
}