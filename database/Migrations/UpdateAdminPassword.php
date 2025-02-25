<?php

namespace Database\Migrations;

use App\Core\Database\Database;

class UpdateAdminPassword {
    private bool $isDev;

    public function __construct() {
        $this->isDev = getenv('APP_ENV') === 'development';
    }

    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ? AND role = ?");
            $stmt->execute([
                password_hash('123456', PASSWORD_DEFAULT),
                'admin@escola.com',
                'admin'
            ]);
            if ($this->isDev) {
                echo "Senha do usuário admin atualizada com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao atualizar senha do usuário admin: " . $e->getMessage() . "\n";
            }
            throw $e;
        }
    }

    public function down() {
        // Não há necessidade de reverter esta migração
        if ($this->isDev) {
            echo "Nada a fazer no down desta migração.\n";
        }
    }
} 