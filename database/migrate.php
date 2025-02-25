<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config\DotEnv;
use App\Core\Database\Migration;

try {
    // Carregar variáveis de ambiente
    (new DotEnv(__DIR__ . '/../.env'))->load();
    
    $isDev = getenv('APP_ENV') === 'development';
    
    if ($isDev) {
        echo "Iniciando processo de migração...\n";
        echo "Carregando variáveis de ambiente...\n";
        echo "Criando instância de Migration...\n";
    }
    
    $migration = new Migration();
    
    if ($isDev) {
        echo "Executando migrações...\n";
    }
    
    $migration->runMigrations();
    
    if ($isDev) {
        echo "Migrações concluídas!\n\n";
        echo "Executando seeds...\n";
    }
    
    $migration->runSeeds();
    
    if ($isDev) {
        echo "Seeds concluídos!\n";
    }
    
} catch (Exception $e) {
    if ($isDev) {
        echo "Erro durante a migração: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    throw $e;
} 