<?php

namespace Chikiday\MultiCryptoApi\Interface;

interface EvmLikeInterface
{
	public function evmCall(
		string $contractAddress,
		string $method,
		?array $args = null,
		?array $argsTypes = null,
	): ?string;
}