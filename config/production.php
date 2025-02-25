<?php

return [
    'database' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'u492226363_school',
        'user' => 'u492226363_admin',
        'password' => getenv('DB_PASSWORD') // Será substituído durante o deploy
    ],
    'app' => [
        'env' => 'production',
        'debug' => false,
        'url' => 'https://seoeads.com/api',
        'cors_origin' => 'https://seoeads.com'
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET') // Será substituído durante o deploy
    ]
]; 