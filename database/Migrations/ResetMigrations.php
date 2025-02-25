<?php

namespace Database\Migrations;

use App\Core\Database\Database;

class ResetMigrations {
    private bool $isDev;

    public function __construct() {
        $this->isDev = getenv('APP_ENV') === 'development';
    }

    public function up() {
        $db = Database::getInstance()->getConnection();

        try {
            $db->exec("DROP TABLE IF EXISTS migrations");
            if ($this->isDev) {
                echo "Tabela migrations removida com sucesso!\n";
            }
        } catch (\PDOException $e) {
            if ($this->isDev) {
                echo "Erro ao remover tabela migrations: " . $e->getMessage() . "\n";
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