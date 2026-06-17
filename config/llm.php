<?php

return [
    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY', ''),
        'api_url' => env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions'),
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    ],
];
