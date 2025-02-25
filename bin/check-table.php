<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Tools\DatabaseChecker;

if ($argc < 2) {
    echo "Uso: php check-table.php <nome_da_tabela>\n";
    exit(1);
}

$tableName = $argv[1];
$checker = new DatabaseChecker();
$result = $checker->checkTable($tableName);

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT); 