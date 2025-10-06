<?php
return [
    'base'    => env('RAJAONGKIR_BASE', 'https://rajaongkir.komerce.id/api/v1/'),
    'key'     => env('RAJAONGKIR_KEY'),
    'auth'    => env('RAJAONGKIR_AUTH', 'key'),
    'timeout' => env('RAJAONGKIR_TIMEOUT', 20),
    'retries' => env('RAJAONGKIR_RETRIES', 2),
    'backoff' => env('RAJAONGKIR_BACKOFF', 200),
];
