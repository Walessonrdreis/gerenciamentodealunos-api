<?php

use App\Core\Database\Database;

class AddNewAdmin {
    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            // Verificar se o usuário já existe
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute(['admin@escola.com']);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                echo "Usuário admin já existe, pulando criação.\n";
                return;
            }

            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $stmt = $db->prepare("INSERT INTO users (id, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $uuid,
                'Administrador',
                'admin@escola.com',
                password_hash('123456', PASSWORD_DEFAULT),
                'admin',
                'active'
            ]);
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
            echo "Usuário admin removido com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao remover usuário admin: " . $e->getMessage() . "\n";
        }
    }
} 