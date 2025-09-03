<?php

return [
	'chains' => [
		'BTC' => [
			'rpc_uri' => env('MCA_BTC_RPC_URI', ''),
			'blockbook_uri' => env('MCA_BTC_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_BTC_RPC_USER', null),
			'password' => env('MCA_BTC_RPC_PASS', null),
		],
		'LTC' => [
			'rpc_uri' => env('MCA_LTC_RPC_URI', ''),
			'blockbook_uri' => env('MCA_LTC_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_LTC_RPC_USER', null),
			'password' => env('MCA_LTC_RPC_PASS', null),
		],
		'DOGE' => [
			'rpc_uri' => env('MCA_DOGE_RPC_URI', ''),
			'blockbook_uri' => env('MCA_DOGE_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_DOGE_RPC_USER', null),
			'password' => env('MCA_DOGE_RPC_PASS', null),
		],
		'DASH' => [
			'rpc_uri' => env('MCA_DASH_RPC_URI', ''),
			'blockbook_uri' => env('MCA_DASH_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_DASH_RPC_USER', null),
			'password' => env('MCA_DASH_RPC_PASS', null),
		],
		'TRX' => [
			'rpc_uri' => env('MCA_TRX_RPC_URI', ''),
			'blockbook_uri' => env('MCA_TRX_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_TRX_RPC_USER', null),
			'password' => env('MCA_TRX_RPC_PASS', null),
		],
		'ZEC' => [
			'rpc_uri' => env('MCA_ZEC_RPC_URI', ''),
			'blockbook_uri' => env('MCA_ZEC_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_ZEC_RPC_USER', null),
			'password' => env('MCA_ZEC_RPC_PASS', null),
		],
		'ETH' => [
			'rpc_uri' => env('MCA_ETH_RPC_URI', ''),
			'blockbook_uri' => env('MCA_ETH_BLOCKBOOK_URI', ''),
			'headers' => [],
			'username' => env('MCA_ETH_RPC_USER', null),
			'password' => env('MCA_ETH_RPC_PASS', null),
		],
	],
];
