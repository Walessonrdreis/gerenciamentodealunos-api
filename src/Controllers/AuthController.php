<?php

namespace App\Controllers;

use App\Core\Database\Database;
use Firebase\JWT\JWT;

class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['senha'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email e senha são obrigatórios']);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            error_log("Dados do usuário: " . print_r($user, true));
            error_log("Senha fornecida: " . $data['senha']);
            error_log("Hash armazenado: " . ($user ? $user['senha'] : 'usuário não encontrado'));

            if (!$user || !password_verify($data['senha'], $user['senha'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciais inválidas']);
                return;
            }

            // Gerar token JWT
            $payload = [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $user['email'],
                'tipo' => $user['tipo'],
                'exp' => time() + (60 * 60 * 24) // Token válido por 24 horas
            ];

            $jwt = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'token' => $jwt,
                'user' => [
                    'id' => $user['id'],
                    'nome' => $user['nome'],
                    'email' => $user['email'],
                    'tipo' => $user['tipo']
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao realizar login: ' . $e->getMessage()]);
        }
    }

    public function verificarToken()
    {
        try {
            $headers = getallheaders();
            $auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';
            
            if (!$auth || !preg_match('/Bearer\s+(.+)/', $auth, $matches)) {
                http_response_code(401);
                echo json_encode(['error' => 'Token não fornecido']);
                return;
            }

            $token = $matches[1];
            $decoded = JWT::decode($token, getenv('JWT_SECRET'), ['HS256']);

            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $decoded->id,
                    'nome' => $decoded->nome,
                    'email' => $decoded->email,
                    'tipo' => $decoded->tipo
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido']);
        }
    }
} 