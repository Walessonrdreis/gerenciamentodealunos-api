<?php

use App\Core\Database\Database;

class UpdateAdminPassword {
    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ? AND role = ?");
            $stmt->execute([
                password_hash('123456', PASSWORD_DEFAULT),
                'admin@escola.com',
                'admin'
            ]);
            echo "Senha do usuário admin atualizada com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao atualizar senha do usuário admin: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        // Não há necessidade de reverter esta migração
        echo "Nada a fazer no down desta migração.\n";
    }
} 