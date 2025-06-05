<?php

namespace Chikiday\MultiCryptoApi\Interface;

use Chikiday\MultiCryptoApi\Model\TokenInfo;

interface TokenAwareInterface
{
	public function getTokenInfo(string $address): ?TokenInfo;

	public function setCacheDir(string $path): self;
}