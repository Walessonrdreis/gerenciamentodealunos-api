<?php

use App\Core\Database\Database;

class CreateUsersTable {
    public function up() {
        $db = Database::getInstance()->getConnection();

        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            tipo ENUM('admin', 'professor', 'aluno') NOT NULL DEFAULT 'aluno',
            status BOOLEAN NOT NULL DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        try {
            $db->exec($sql);
            echo "Tabela 'users' criada com sucesso!\n";

            // Criar usuário admin padrão
            $senha = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Administrador', 'admin@seoeads.com', $senha, 'admin']);
            echo "Usuário admin criado com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao criar tabela 'users': " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        $sql = "DROP TABLE IF EXISTS users";

        try {
            $db->exec($sql);
            echo "Tabela 'users' removida com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao remover tabela 'users': " . $e->getMessage() . "\n";
        }
    }
} 