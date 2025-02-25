<?php

use App\Core\Database\Database;

class AddNewAdmin {
    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            // Criar novo usuário admin
            $senha = password_hash('123456', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Administrador', 'admin@escola.com', $senha, 'admin']);
            echo "Novo usuário admin criado com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao criar novo usuário admin: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
            $stmt->execute(['admin@escola.com']);
            echo "Novo usuário admin removido com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao remover novo usuário admin: " . $e->getMessage() . "\n";
        }
    }
} 