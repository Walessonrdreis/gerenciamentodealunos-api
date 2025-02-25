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
            
            // Verificar credenciais (exemplo simplificado)
            if ($data['username'] === 'testuser' && $data['password'] === 'testpassword') {
                $payload = [
                    'iss' => 'https://seoeads.com',
                    'aud' => 'https://seoeads.com',
                    'iat' => time(),
                    'nbf' => time(),
                    'exp' => time() + 3600,
                    'data' => [
                        'username' => $data['username']
                    ]
                ];

                $jwt = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

                echo json_encode(['token' => $jwt]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciais inválidas']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor']);
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