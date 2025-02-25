<?php

use App\Core\Database\Database;

class ResetMigrations {
    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            $db->exec("DROP TABLE IF EXISTS migrations");
            echo "Tabela migrations removida com sucesso!\n";
        } catch (\PDOException $e) {
            echo "Erro ao remover tabela migrations: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        // Não há necessidade de reverter esta migração
        echo "Nada a fazer no down desta migração.\n";
    }
} 