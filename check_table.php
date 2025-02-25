<?php

try {
    // Configurações do banco de dados
    $host = '127.0.0.1';
    $dbname = 'u492226363_escola';
    $username = 'u492226363_escola';
    $password = 'Escola@123';
    
    // Conectar ao banco de dados
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar a estrutura da tabela users
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Estrutura da tabela users:\n";
    print_r($columns);
    
    // Verificar os dados da tabela users
    $stmt = $db->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nDados da tabela users:\n";
    print_r($users);
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
} 