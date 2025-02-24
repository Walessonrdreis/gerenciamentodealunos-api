<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config\DotEnv;
use App\Core\Database\Migration;

try {
    echo "Iniciando processo de migração...\n";
    
    // Carregar variáveis de ambiente
    echo "Carregando variáveis de ambiente...\n";
    (new DotEnv(__DIR__ . '/../.env'))->load();
    
    // Executar migrações
    echo "Criando instância de Migration...\n";
    $migration = new Migration();
    
    echo "Executando migrações...\n";
    $migration->runMigrations();
    echo "Migrações concluídas!\n\n";
    
    echo "Executando seeds...\n";
    $migration->runSeeds();
    echo "Seeds concluídos!\n";
    
} catch (Exception $e) {
    echo "Erro durante a migração: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 