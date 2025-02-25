<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database\Database;
use App\Core\Config\DotEnv;

// Carregar variáveis de ambiente
(new DotEnv(__DIR__ . '/../.env'))->load();

$isDev = getenv('APP_ENV') === 'development';

try {
    // Conectar ao banco de dados
    $db = Database::getInstance()->getConnection();
    
    // Buscar usuário admin
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@escola.com']);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($user) {
        if ($isDev) {
            echo "Usuário encontrado:\n";
            echo "ID: " . $user['id'] . "\n";
            echo "Nome: " . $user['name'] . "\n";
            echo "Email: " . $user['email'] . "\n";
            echo "Role: " . $user['role'] . "\n";
            echo "Status: " . $user['status'] . "\n";
            echo "Senha hash: " . $user['password'] . "\n";
        }
    } else {
        if ($isDev) {
            echo "Usuário admin@escola.com não encontrado.\n";
        }
        
        // Criar usuário admin
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $id = uniqid();
        
        $stmt = $db->prepare("INSERT INTO users (id, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, 'Administrador', 'admin@escola.com', $password, 'admin', 'active']);
        
        if ($isDev) {
            echo "\nUsuário admin criado com sucesso!\n";
            echo "ID: " . $id . "\n";
            echo "Email: admin@escola.com\n";
            echo "Senha: 123456\n";
        }
    }
} catch (\Exception $e) {
    if ($isDev) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    throw $e;
} 