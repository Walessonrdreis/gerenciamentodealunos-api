<?php
namespace App\Core\Database;

class Migration {
    private \PDO $db;
    private string $migrationsPath;
    private bool $isDev;

    public function __construct() {
        $this->isDev = getenv('APP_ENV') === 'development';
        $this->db = Database::getInstance()->getConnection();
        $this->migrationsPath = __DIR__ . '/../../../database';
        
        if ($this->isDev) {
            echo "Iniciando construtor da Migration...\n";
            echo "Caminhos configurados:\n";
            echo "Migrations: {$this->migrationsPath}\n";
        }
    }

    public function runMigrations(): void {
        try {
            if ($this->isDev) {
                echo "Executando migrações...\n";
            }

            $sqlFile = $this->migrationsPath . '/migrations.sql';
            if (!file_exists($sqlFile)) {
                throw new \Exception("Arquivo de migrações não encontrado: migrations.sql");
            }

            $sql = file_get_contents($sqlFile);
            if ($sql === false) {
                throw new \Exception("Erro ao ler arquivo de migrações");
            }

            // Executar cada comando SQL separadamente
            $commands = array_filter(
                array_map('trim', explode(';', $sql)),
                function($cmd) { return !empty($cmd); }
            );

            foreach ($commands as $command) {
                if ($this->isDev) {
                    echo "Executando comando: " . substr($command, 0, 50) . "...\n";
                }
                $this->db->exec($command);
            }

            if ($this->isDev) {
                echo "Migrações executadas com sucesso!\n";
            }
        } catch (\Exception $e) {
            if ($this->isDev) {
                echo "Erro ao executar migrações: " . $e->getMessage() . "\n";
                echo "Stack trace: " . $e->getTraceAsString() . "\n";
            }
            throw $e;
        }
    }

    public function runSeeds(): void {
        // Seeds já estão incluídos no arquivo migrations.sql
        return;
    }
} 