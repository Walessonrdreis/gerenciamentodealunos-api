<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Tools\DatabaseChecker;

if ($argc < 2) {
    echo "Uso: php check-user.php <email>\n";
    exit(1);
}

$email = $argv[1];
$checker = new DatabaseChecker();
$result = $checker->checkUser($email);

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT); 