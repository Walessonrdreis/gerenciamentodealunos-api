<?php

use App\Core\Database\Database;

class CreateAlunosTable {
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
            user_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )";

        try {
            $db->exec($sql);
            echo "Tabela 'alunos' criada com sucesso!\n";

            // Criar alguns alunos de exemplo
            $alunos = [
                [
                    'nome' => 'João da Silva',
                    'email' => 'joao@escola.com',
                    'data_nascimento' => '2000-01-01',
                    'cpf' => '123.456.789-00',
                    'telefone' => '(11) 98765-4321',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP'
                ],
                [
                    'nome' => 'Maria Santos',
                    'email' => 'maria@escola.com',
                    'data_nascimento' => '2001-02-02',
                    'cpf' => '987.654.321-00',
                    'telefone' => '(11) 91234-5678',
                    'cidade' => 'Rio de Janeiro',
                    'estado' => 'RJ'
                ]
            ];

            $stmt = $db->prepare("INSERT INTO alunos (nome, email, data_nascimento, cpf, telefone, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($alunos as $aluno) {
                $stmt->execute([
                    $aluno['nome'],
                    $aluno['email'],
                    $aluno['data_nascimento'],
                    $aluno['cpf'],
                    $aluno['telefone'],
                    $aluno['cidade'],
                    $aluno['estado']
                ]);
            }
            
            echo "Alunos de exemplo criados com sucesso!\n";
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