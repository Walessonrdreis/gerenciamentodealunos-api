<?php
namespace App\Core\Database;

class Migration {
    private \PDO $db;
    private string $migrationsPath;
    private string $seedsPath;

    private array $classNameMap = [
        '001_create_users_table' => 'CreateUsersTable',
        '002_create_alunos_table' => 'CreateAlunosTable',
        '003_add_new_admin' => 'AddNewAdmin',
        '004_update_admin_password' => 'UpdateAdminPassword',
        '005_reset_migrations' => 'ResetMigrations'
    ];

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
            echo "Verificando arquivos de migration...\n";
            $files = scandir($this->migrationsPath);
            $toApplyMigrations = array_diff($files, ['.', '..', '.gitkeep']);
            
            // Filtra apenas arquivos PHP
            $phpMigrations = array_filter($toApplyMigrations, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'php';
            });
            
            // Ordena as migrações
            sort($phpMigrations);
            
            echo "Migrations PHP para aplicar: " . implode(", ", $phpMigrations) . "\n";

            // Primeiro executa a migração de reset se existir
            foreach ($phpMigrations as $key => $migration) {
                if (strpos($migration, 'reset_migrations') !== false) {
                    echo "Executando migração de reset: $migration\n";
                    require_once $this->migrationsPath . '/' . $migration;
                    $className = $this->classNameMap[pathinfo($migration, PATHINFO_FILENAME)] ?? null;
                    if ($className && class_exists($className)) {
                        $migrationInstance = new $className();
                        $migrationInstance->up();
                    }
                    unset($phpMigrations[$key]);
                    break;
                }
            }

            echo "Criando tabela de migrations...\n";
            $this->createMigrationsTable();

            echo "Obtendo migrations já aplicadas...\n";
            $appliedMigrations = $this->getAppliedMigrations();
            echo "Migrations já aplicadas: " . implode(", ", $appliedMigrations) . "\n";

            foreach ($phpMigrations as $migration) {
                $baseName = pathinfo($migration, PATHINFO_FILENAME);
                if (!in_array($baseName, $appliedMigrations)) {
                    echo "Aplicando migration: $migration\n";
                    require_once $this->migrationsPath . '/' . $migration;
                    
                    $className = $this->classNameMap[$baseName] ?? null;
                    if (!$className || !class_exists($className)) {
                        echo "Aviso: Classe $className não encontrada no arquivo $migration\n";
                        continue;
                    }
                    
                    $migrationInstance = new $className();
                    $migrationInstance->up();
                    
                    $this->logMigration($baseName);
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
        $migrations = $statement->fetchAll(\PDO::FETCH_COLUMN);
        
        // Remove a extensão dos nomes das migrações
        return array_map(function($migration) {
            return pathinfo($migration, PATHINFO_FILENAME);
        }, $migrations);
    }

    private function logMigration(string $migration): void {
        $statement = $this->db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        $statement->execute(['migration' => $migration]);
    }
} 