<?php

return [
    'processors' => [
        'paypal' => [
            'name' => 'PayPal',
            'type' => 'PayPal account'
        ],
        'stripe' => [
            'name' => 'Stripe',
            'type' => 'Credit card'
        ],
        'razorpay' => [
            'name' => 'Razorpay',
            'type' => 'Credit card'
        ],
        'paystack' => [
            'name' => 'Paystack',
            'type' => 'Credit card'
        ],
        'coinbase' => [
            'name' => 'Coinbase',
            'type' => 'Cryptocurrency'
        ],
        'cryptocom' => [
            'name' => 'Crypto.com',
            'type' => 'Cryptocurrency'
        ],
        'bank' => [
            'name' => 'Bank',
            'type' => 'Bank transfer'
        ],
        'brc20' => [
            'name' => 'BRC20',
            'type' => 'BRC20 transfer'
        ]
    ]
];