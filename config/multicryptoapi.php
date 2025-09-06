<?php

return [
    // Default chain used when calling MultiCryptoApi::<method>()
    'default' => env('MULTICRYPTOAPI_DEFAULT', 'BTC'),

    // Configure each chain with its RPC and Blockbook endpoints and optional headers/auth.
    // Provide values via your application's .env file.
    'chains' => [
        'BTC' => [
            'uri' => env('BTC_RPC_URI'),
            'blockbook_uri' => env('BTC_BLOCKBOOK_URI'),
            'username' => env('BTC_RPC_USER'),
            'password' => env('BTC_RPC_PASSWORD'),
            'headers' => array_filter([
                'api-key' => env('NODES_API_KEY'),
            ]),
        ],
        'LTC' => [
            'uri' => env('LTC_RPC_URI'),
            'blockbook_uri' => env('LTC_BLOCKBOOK_URI'),
            'username' => env('LTC_RPC_USER'),
            'password' => env('LTC_RPC_PASSWORD'),
            'headers' => array_filter([
                'api-key' => env('NODES_API_KEY'),
            ]),
        ],
        'DOGE' => [
            'uri' => env('DOGE_RPC_URI'),
            'blockbook_uri' => env('DOGE_BLOCKBOOK_URI'),
            'username' => env('DOGE_RPC_USER'),
            'password' => env('DOGE_RPC_PASSWORD'),
            'headers' => array_filter([
                'api-key' => env('NODES_API_KEY'),
            ]),
        ],
        'DASH' => [
            'uri' => env('DASH_RPC_URI'),
            'blockbook_uri' => env('DASH_BLOCKBOOK_URI'),
            'username' => env('DASH_RPC_USER'),
            'password' => env('DASH_RPC_PASSWORD'),
            'headers' => array_filter([
                'api-key' => env('NODES_API_KEY'),
            ]),
        ],
        'ZEC' => [
            'uri' => env('ZEC_RPC_URI'),
            'blockbook_uri' => env('ZEC_BLOCKBOOK_URI'),
            'username' => env('ZEC_RPC_USER'),
            'password' => env('ZEC_RPC_PASSWORD'),
            'headers' => array_filter([
                'api-key' => env('NODES_API_KEY'),
            ]),
        ],
        'ETH' => [
            'uri' => env('ETH_RPC_URI'),
            'blockbook_uri' => env('ETH_BLOCKBOOK_URI'),
            'headers' => array_filter([
                'api-key' => env('INFURA_API_KEY'), // or any gateway header
            ]),
        ],
        'TRX' => [
            'uri' => env('TRX_RPC_URI'),
            'blockbook_uri' => env('TRX_BLOCKBOOK_URI'),
            'headers' => array_filter([
                'TRONSCAN-API-KEY' => env('TRONSCAN_API_KEY'),
                'TRONGRID-API-KEY' => env('TRONGRID_API_KEY'),
            ]),
        ],
    ],
];
