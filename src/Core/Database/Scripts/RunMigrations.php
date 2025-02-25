<?php

namespace App\Core\Database\Scripts;

use Dotenv\Dotenv;
use App\Core\Database\Migration;
use Exception;

class RunMigrations {
    private string $migrationKey;
    private Migration $migration;
    private bool $isDev;

    public function __construct(string $migrationKey) {
        $this->loadEnvironment();
        $this->migrationKey = $migrationKey;
        $this->isDev = getenv('APP_ENV') === 'development';
        $this->migration = new Migration();
    }

    private function loadEnvironment(): void {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 4));
        $dotenv->load();
    }

    private function validateKey(): bool {
        $expectedKey = 'migration_key_' . date('Ymd');
        return $this->migrationKey === $expectedKey;
    }

    public function execute(): array {
        try {
            if (!$this->validateKey()) {
                return [
                    'success' => false,
                    'error' => 'Chave de migração inválida'
                ];
            }

            $this->migration->runMigrations();
            $this->migration->runSeeds();

            return [
                'success' => true,
                'message' => 'Migrações executadas com sucesso'
            ];

        } catch (Exception $e) {
            if ($this->isDev) {
                error_log("Erro durante a migração: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
            }
            return [
                'success' => false,
                'error' => $this->isDev ? $e->getMessage() : 'Erro ao executar migrações'
            ];
        }
    }
} 