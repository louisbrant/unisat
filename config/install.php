<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    */
    'php_version' => '8.0.2',

    'extensions' => [
        'php' => [
            'BCMath',
            'Ctype',
            'Fileinfo',
            'JSON',
            'Mbstring',
            'OpenSSL',
            'PDO',
            'Tokenizer',
            'XML',
            'GD',
            'cURL'
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File permissions
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'Files' => [
            '.env',
        ],
        'Folders' =>
            [
                'bootstrap/cache',
                'lang',
                'public/uploads/brand',
                'public/uploads/users/images',
                'public/uploads/users/transcriptions',
                'storage',
                'storage/app/purifier',
                'storage/framework',
                'storage/framework/cache',
                'storage/framework/cache/data',
                'storage/framework/sessions',
                'storage/framework/views',
                'storage/logs',
            ],
    ]
];
