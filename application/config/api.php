<?php
$config = ($config ?? []) + [
    'rsv_sale' => [
        'base_uri' => 'https://api.github.com', //env('API_BASE_URI'),
        'common' => [
            'client_id' => '', //env('API_CLIENT_ID'),
            'api_key' => '', //env('API_KEY'),
            'language_code' => 'jp',
        ],
    ],
    'android' => [
        'base_uri' => 'http://httpbin.org', //env('ANA_BASE_URI'),
        'url_ok' => '', //'ana/login/callback',
        'url_fail' => '', //'ana/login/callback',
        'sso_product' => '', //env('SSO_PRODUCT'),
        'gift_code' => '', //env('GIFT_CODE'),
        'basic_user' => '', //env('BASIC_USER'),
        'basic_pwd' => '', //env('BASIC_PWD'),
    ],
    'max_execution_time' => '', //env('MAX_EXECUTION_TIME', 60),
];
