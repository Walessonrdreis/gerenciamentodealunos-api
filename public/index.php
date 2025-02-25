<?php
// Corrigindo o caminho do autoload
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config\DotEnv;
use App\Core\Router\Router;
use App\Core\Database\Database;

try {
    // Carregar variáveis de ambiente
    (new DotEnv(__DIR__ . '/../.env'))->load();

    // Configurar CORS baseado no ambiente
    $allowedOrigin = getenv('CORS_ALLOW_ORIGIN') ?: '*';
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    // Habilitar logs de erro em desenvolvimento
    if ($allowedOrigin === '*') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    // Debug
    error_log("Request URI: " . $_SERVER['REQUEST_URI']);
    error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Request Body: " . file_get_contents('php://input'));

    // Se for uma requisição OPTIONS, retornar apenas os headers
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // Configurar banco de dados
    Database::getInstance()->connect();

    // Configurar rotas
    $router = new Router();
    require_once __DIR__ . '/../src/routes/api.php';

    // Executar roteamento
    $router->handle();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
} 