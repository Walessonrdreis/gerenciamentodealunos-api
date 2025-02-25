<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Config\DotEnv;

// Carregar variáveis de ambiente
(new DotEnv(__DIR__ . '/.env'))->load();

// Verificar senha de migração
$migrationKey = $_GET['key'] ?? '';
if ($migrationKey !== getenv('JWT_SECRET')) {
    http_response_code(401);
    echo json_encode(['error' => 'Chave de migração inválida']);
    exit;
}

try {
    ob_start(); // Captura a saída do script
    require_once __DIR__ . '/database/migrate.php';
    $output = ob_get_clean(); // Pega a saída capturada
    
    echo json_encode([
        'success' => true, 
        'message' => 'Migrações executadas com sucesso',
        'details' => $output
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao executar migrações',
        'message' => $e->getMessage(),
        'details' => $e->getTraceAsString()
    ]);
} 