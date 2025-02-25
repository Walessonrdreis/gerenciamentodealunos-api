<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Database\Database;

// Carregar variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Configurações do banco de dados
    $host = '127.0.0.1';
    $dbname = 'u492226363_escola';
    $username = 'u492226363_escola';
    $password = 'Escola@123';
    
    // Conectar ao banco de dados
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar o usuário admin
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@escola.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "Usuário encontrado:\n";
        print_r($user);
        
        // Testar a senha
        $senha = '123456';
        $senhaCorreta = password_verify($senha, $user['senha']);
        echo "\nSenha '123456' está " . ($senhaCorreta ? "correta" : "incorreta") . "\n";
        
        if (!$senhaCorreta) {
            echo "Hash atual: " . $user['senha'] . "\n";
            echo "Novo hash: " . password_hash($senha, PASSWORD_DEFAULT) . "\n";
        }
    } else {
        echo "Usuário não encontrado\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
} 