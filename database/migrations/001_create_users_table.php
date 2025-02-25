<?php

use App\Core\Database\Database;

class CreateUsersTable {
    public function up() {
        $db = Database::getInstance()->getConnection();

        $sql = "CREATE TABLE IF NOT EXISTS users (
            id VARCHAR(36) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        try {
            $db->exec($sql);
            echo "Tabela 'users' criada com sucesso!\n";
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