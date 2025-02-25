<?php

namespace App\Core\Tools;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DatabaseChecker
{
    private PDO $conn;
    private bool $isDev;

    public function __construct()
    {
        $this->isDev = getenv('APP_ENV') === 'development';
        $this->loadEnvironment();
        $this->connect();
    }

    private function loadEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
        $dotenv->load();
    }

    private function connect(): void
    {
        try {
            $this->conn = new PDO(
                "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            if ($this->isDev) {
                error_log("Erro de conexão: " . $e->getMessage());
            }
            exit(1);
        }
    }

    public function checkTable(string $tableName): array
    {
        try {
            $stmt = $this->conn->prepare("SHOW TABLES LIKE :table");
            $stmt->execute(['table' => $tableName]);
            
            if ($stmt->rowCount() > 0) {
                $columns = $this->getTableColumns($tableName);
                return [
                    'success' => true,
                    'message' => "Tabela '$tableName' existe",
                    'columns' => $columns
                ];
            }
            
            return [
                'success' => false,
                'message' => "Tabela '$tableName' não existe"
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $this->isDev ? $e->getMessage() : 'Erro ao verificar tabela'
            ];
        }
    }

    private function getTableColumns(string $tableName): array
    {
        $stmt = $this->conn->prepare("DESCRIBE $tableName");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkUser(string $email): array
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'success' => true,
                    'message' => "Usuário encontrado",
                    'user' => $user
                ];
            }
            
            return [
                'success' => false,
                'message' => "Usuário não encontrado"
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $this->isDev ? $e->getMessage() : 'Erro ao verificar usuário'
            ];
        }
    }
} 