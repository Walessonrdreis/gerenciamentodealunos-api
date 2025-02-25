<?php

use App\Core\Database\Database;

class CreateAlunosTable {
    private bool $isDev;

    public function __construct() {
        $this->isDev = getenv('APP_ENV') === 'development';
    }

    public function up() {
        $db = Database::getInstance()->getConnection();

        $sql = "CREATE TABLE IF NOT EXISTS alunos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            data_nascimento DATE,
            cpf VARCHAR(14) UNIQUE,
            telefone VARCHAR(20),
            endereco TEXT,
            cidade VARCHAR(100),
            estado CHAR(2),
            cep VARCHAR(9),
            status ENUM('ativo', 'inativo', 'trancado') NOT NULL DEFAULT 'ativo',
            observacoes TEXT,
            user_id VARCHAR(36),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )";

        try {
            $db->exec($sql);
            if ($this->isDev) {
                echo "Tabela 'alunos' criada com sucesso!\n";
            }

            // Criar alguns alunos de exemplo
            $sql = "INSERT IGNORE INTO alunos (nome, email, data_nascimento, cpf, telefone, cidade, estado) VALUES 
                ('João da Silva', 'joao@escola.com', '2000-01-01', '123.456.789-00', '(11) 98765-4321', 'São Paulo', 'SP'),
                ('Maria Santos', 'maria@escola.com', '2001-02-02', '987.654.321-00', '(11) 91234-5678', 'Rio de Janeiro', 'RJ')";
            
            $db->exec($sql);
            if ($this->isDev) {
                echo "Alunos de exemplo criados com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao criar tabela 'alunos': " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        $sql = "DROP TABLE IF EXISTS alunos";

        try {
            $db->exec($sql);
            if ($this->isDev) {
                echo "Tabela 'alunos' removida com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao remover tabela 'alunos': " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }
} 