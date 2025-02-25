<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private bool $isDev;

    private function __construct()
    {
        $this->isDev = getenv('APP_ENV') === 'development';
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect(): PDO
    {
        if ($this->connection === null) {
            try {
                $host = getenv('DB_HOST');
                $port = getenv('DB_PORT');
                $database = getenv('DB_DATABASE');
                $username = getenv('DB_USERNAME');
                $password = getenv('DB_PASSWORD');

                $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
                
                $this->connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new PDOException($this->isDev ? 
                    "Erro na conexão com o banco de dados: " . $e->getMessage() : 
                    "Erro na conexão com o banco de dados");
            }
        }

        return $this->connection;
    }

    public function getConnection(): PDO
    {
        return $this->connection ?? $this->connect();
    }
} 