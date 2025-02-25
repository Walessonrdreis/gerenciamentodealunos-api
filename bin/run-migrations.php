<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database\Scripts\RunMigrations;

header('Content-Type: application/json');

$migrationKey = $_GET['key'] ?? '';
$runner = new RunMigrations($migrationKey);
$result = $runner->execute();

if (!$result['success']) {
    http_response_code(400);
}

echo json_encode($result); 