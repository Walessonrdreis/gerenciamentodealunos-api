<?php

namespace App\Controllers;

use App\Core\Database\Database;
use Firebase\JWT\JWT;

class AuthController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login()
    {
        try {
            // Obter dados do corpo da requisição
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email e senha são obrigatórios']);
                return;
            }

            // Buscar usuário pelo email
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND status = 'active'");
            $stmt->execute(['email' => $data['email']]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($data['password'], $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciais inválidas']);
                return;
            }

            // Gerar token JWT
            $payload = [
                'sub' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'iat' => time(),
                'exp' => time() + (60 * 60) // 1 hora
            ];

            $jwt = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

            // Atualizar último login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt->execute(['id' => $user['id']]);

            // Retornar resposta
            echo json_encode([
                'token' => $jwt,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor']);
        }
    }
} 