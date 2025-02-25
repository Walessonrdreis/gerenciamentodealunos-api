<?php

namespace App\Controllers;

use App\Core\Database\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
            
            if (!isset($data['email']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email e password são obrigatórios']);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            error_log("Query SQL: SELECT * FROM users WHERE email = '" . $data['email'] . "' AND status = 'active'");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            error_log("Dados do usuário: " . print_r($user, true));
            error_log("Password fornecido: " . $data['password']);
            error_log("Hash armazenado: " . ($user ? $user['password'] : 'usuário não encontrado'));
            error_log("Colunas disponíveis: " . implode(", ", array_keys($user ?? [])));
            error_log("Resultado do password_verify: " . (password_verify($data['password'], $user['password'] ?? '') ? 'true' : 'false'));

            if (!$user || !password_verify($data['password'], $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciais inválidas']);
                return;
            }

            // Gerar token JWT
            $payload = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'exp' => time() + (60 * 60 * 24) // Token válido por 24 horas
            ];

            $jwt = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
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
            echo json_encode(['error' => 'Erro ao realizar login: ' . $e->getMessage()]);
        }
    }

    public function verificarToken()
    {
        error_log("=== Iniciando verificação de token ===");
        try {
            $headers = getallheaders();
            error_log("Headers recebidos: " . json_encode($headers));

            if (!isset($headers['Authorization'])) {
                error_log("Token não fornecido no header Authorization");
                http_response_code(401);
                echo json_encode(['error' => 'Token não fornecido']);
                return;
            }

            $auth = $headers['Authorization'];
            error_log("Header Authorization: " . $auth);

            if (!preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                error_log("Token não está no formato Bearer");
                http_response_code(401);
                echo json_encode(['error' => 'Token inválido']);
                return;
            }

            $token = $matches[1];
            error_log("Token extraído: " . $token);

            try {
                $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
                error_log("Token decodificado com sucesso: " . json_encode($decoded));
                echo json_encode(['success' => true, 'user' => $decoded]);
            } catch (\Exception $e) {
                error_log("Erro ao decodificar token: " . $e->getMessage());
                http_response_code(401);
                echo json_encode(['error' => 'Token inválido', 'details' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            error_log("Erro na verificação do token: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor', 'details' => $e->getMessage()]);
        }
    }
} 