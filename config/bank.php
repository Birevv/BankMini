<?php

return [
    'name' => env('BANK_NAME', 'Bank Mini Sekolah'),
    'account_prefix' => env('BANK_ACCOUNT_PREFIX', 'BM'),
    'minimum_balance' => (int) env('BANK_MINIMUM_BALANCE', 10_000),
    'accounts' => [
        'cash' => '101',
        'customer_savings' => '201',
    ],
];
