<?php
namespace App\Core\Database;

class Migration {
    private \PDO $db;
    private string $migrationsPath;
    private string $seedsPath;

    public function __construct() {
        echo "Iniciando construtor da Migration...\n";
        $this->db = Database::getInstance()->getConnection();
        $this->migrationsPath = __DIR__ . '/../../../database/migrations';
        $this->seedsPath = __DIR__ . '/../../../database/seeds';
        echo "Caminhos configurados:\n";
        echo "Migrations: {$this->migrationsPath}\n";
        echo "Seeds: {$this->seedsPath}\n";
    }

    public function runMigrations(): void {
        try {
            echo "Criando tabela de migrations...\n";
            $this->createMigrationsTable();

            echo "Obtendo migrations já aplicadas...\n";
            $appliedMigrations = $this->getAppliedMigrations();
            echo "Migrations já aplicadas: " . implode(", ", $appliedMigrations) . "\n";

            echo "Verificando arquivos de migration...\n";
            $files = scandir($this->migrationsPath);
            $toApplyMigrations = array_diff($files, ['.', '..', '.gitkeep']);
            echo "Migrations para aplicar: " . implode(", ", $toApplyMigrations) . "\n";

            foreach ($toApplyMigrations as $migration) {
                if (!in_array($migration, $appliedMigrations)) {
                    echo "Aplicando migration: $migration\n";
                    require_once $this->migrationsPath . '/' . $migration;
                    
                    $className = pathinfo($migration, PATHINFO_FILENAME);
                    $migrationInstance = new $className();
                    $migrationInstance->up();
                    
                    $this->logMigration($migration);
                    echo "Migration aplicada com sucesso: $migration\n";
                }
            }
        } catch (\Exception $e) {
            echo "Erro ao executar migrações: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }

    public function runSeeds(): void {
        try {
            echo "Iniciando seeds...\n";
            $files = scandir($this->seedsPath);
            $seedFiles = array_diff($files, ['.', '..', '.gitkeep']);
            echo "Seeds para executar: " . implode(", ", $seedFiles) . "\n";

            foreach ($seedFiles as $seed) {
                echo "Executando seed: $seed\n";
                require_once $this->seedsPath . '/' . $seed;
                
                $className = pathinfo($seed, PATHINFO_FILENAME);
                $seedInstance = new $className();
                $seedInstance->run();
                
                echo "Seed executado com sucesso: $seed\n";
            }
        } catch (\Exception $e) {
            echo "Erro ao executar seeds: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }

    private function createMigrationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $this->db->exec($sql);
    }

    private function getAppliedMigrations(): array {
        $statement = $this->db->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function logMigration(string $migration): void {
        $statement = $this->db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        $statement->execute(['migration' => $migration]);
    }
} 