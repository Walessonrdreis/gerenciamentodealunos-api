<?php

use App\Core\Database\Database;

class UpdateAdminPassword {
    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            // Atualizar senha do usuário admin
            $senha = password_hash('123456', PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET senha = ? WHERE email = ?");
            $stmt->execute([$senha, 'admin@escola.com']);
            echo "Senha do usuário admin atualizada com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao atualizar senha do usuário admin: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        $db = Database::getInstance()->getConnection();

        try {
            // Restaurar senha anterior (você pode definir uma senha padrão aqui)
            $senha = password_hash('senha_anterior', PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET senha = ? WHERE email = ?");
            $stmt->execute([$senha, 'admin@escola.com']);
            echo "Senha do usuário admin restaurada com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao restaurar senha do usuário admin: " . $e->getMessage() . "\n";
        }
    }
} 