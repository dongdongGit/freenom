<?php

return [
    'username' => env('FREENOM_USERNAME'),
    'password' => env('FREENOM_PASSWORD'),

    'timeout' => env('FREENOM_CURL_TIMEOUT', 15.0),

    'log' => [
        'level' => env('FREENOM_LOG_LEVEL', 'debug'),
        'file'  => env('FREENOM_LOG_FILE', storage_path('logs/freenom.log')),
    ],
];
