<?php

use App\Core\Database\Database;

class CreateUsersTable {
    private bool $isDev;

    public function __construct() {
        $this->isDev = getenv('APP_ENV') === 'development';
    }

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
            if ($this->isDev) {
                echo "Tabela 'users' criada com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao criar tabela 'users': " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        $sql = "DROP TABLE IF EXISTS users";

        try {
            $db->exec($sql);
            if ($this->isDev) {
                echo "Tabela 'users' removida com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao remover tabela 'users': " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }
} 