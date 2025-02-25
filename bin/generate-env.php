<?php

if ($argc < 2) {
    echo "Uso: php generate-env.php <ambiente>\n";
    exit(1);
}

$env = $argv[1];
$configFile = __DIR__ . "/../config/{$env}.php";

if (!file_exists($configFile)) {
    echo "Arquivo de configuração não encontrado: {$configFile}\n";
    exit(1);
}

$config = require $configFile;
$envContent = "";

// Database
$envContent .= "DB_HOST=" . $config['database']['host'] . "\n";
$envContent .= "DB_PORT=" . $config['database']['port'] . "\n";
$envContent .= "DB_DATABASE=" . $config['database']['name'] . "\n";
$envContent .= "DB_USERNAME=" . $config['database']['user'] . "\n";
$envContent .= "DB_PASSWORD=" . $config['database']['password'] . "\n";

// App
$envContent .= "APP_ENV=" . $config['app']['env'] . "\n";
$envContent .= "APP_DEBUG=" . ($config['app']['debug'] ? 'true' : 'false') . "\n";
$envContent .= "APP_URL=" . $config['app']['url'] . "\n";
$envContent .= "CORS_ALLOW_ORIGIN=" . $config['app']['cors_origin'] . "\n";

// JWT
$envContent .= "JWT_SECRET=" . $config['jwt']['secret'] . "\n";

file_put_contents(__DIR__ . "/../.env", $envContent);
echo "Arquivo .env gerado com sucesso!\n"; 