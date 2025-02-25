<?php

use App\Core\Database\Database;

class CreateAlunosTable {
    public function up() {
        $db = Database::getInstance()->getConnection();

        $sql = "CREATE TABLE IF NOT EXISTS alunos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        try {
            $db->exec($sql);
            echo "Tabela 'alunos' criada com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao criar tabela 'alunos': " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        $sql = "DROP TABLE IF EXISTS alunos";

        try {
            $db->exec($sql);
            echo "Tabela 'alunos' removida com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao remover tabela 'alunos': " . $e->getMessage() . "\n";
        }
    }
} 