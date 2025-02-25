<?php

namespace Database\Migrations;

use App\Core\Database\Database;

class AddNewAdmin {
    private bool $isDev;

    public function __construct() {
        $this->isDev = getenv('APP_ENV') === 'development';
    }

    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            // Verificar se o usuário já existe
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute(['admin@escola.com']);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                if ($this->isDev) {
                    echo "Usuário admin já existe, pulando criação.\n";
                }
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
            if ($this->isDev) {
                echo "Novo usuário admin criado com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao criar novo usuário admin: " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
            $stmt->execute(['admin@escola.com']);
            if ($this->isDev) {
                echo "Usuário admin removido com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao remover usuário admin: " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }
} 