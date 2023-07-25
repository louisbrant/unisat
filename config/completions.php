<?php

return [

    'tones' => [
        'professional'  => ['emoji' => '🤵', 'name' => 'Professional'],
        'childish'      => ['emoji' => '😜', 'name' => 'Childish'],
        'luxurious'     => ['emoji' => '💎', 'name' => 'Luxurious'],
        'confident'     => ['emoji' => '💪', 'name' => 'Confident'],
        'friendly'      => ['emoji' => '😊', 'name' => 'Friendly'],
        'exciting'      => ['emoji' => '😃', 'name' => 'Exciting'],
        'casual'        => ['emoji' => '😎', 'name' => 'Casual'],
        'dramatic'      => ['emoji' => '🎭', 'name' => 'Dramatic'],
        'masculine'     => ['emoji' => '👨‍💼', 'name' => 'Masculine'],
        'feminine'      => ['emoji' => '👩‍💼', 'name' => 'Feminine'],
    ],

    'languages' => [
        'ar', 'az', 'bn', 'br', 'cs', 'de', 'da', 'el', 'en', 'es', 'fa', 'fi', 'fr', 'he', 'hi', 'hr', 'hu', 'id', 'in', 'it', 'ja', 'km', 'ko', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'si', 'sk', 'sl', 'sv', 'th', 'tr', 'vi', 'zh'
    ],

    'creativities' => [
        'repetitive'    => 0,
        'deterministic' => 0.25,
        'original'      => 0.50,
        'creative'      => 0.75,
        'imaginative'   => 1
    ],

    'variations' => [
        1,
        2,
        3,
        4
    ],

    'lengths' => [
        'short',
        'medium',
        'long'
    ],

    'ratios' => [
        [
            'scripts' => [
                'Hiragana',
                'Katakana',
                'Han'
            ],
            'value' => 0.75
        ], [
            'scripts' => [
                'Hangul'
            ],
            'value' => 0.50
        ], [
            'scripts' => [
                'Khmer',
                'Lao',
                'Thai'
            ],
            'value' => 0.25
        ]
    ]
];
