<?php

namespace App\Controllers;

use App\Core\Database\Database;

class UserController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT id, nome, email, tipo, status, created_at, updated_at FROM users");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao listar usuários: ' . $e->getMessage()]);
        }
    }

    public function criar() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nome']) || !isset($data['email']) || !isset($data['senha'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Nome, email e senha são obrigatórios']);
                return;
            }

            // Verifica se o email já existe
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Email já cadastrado']);
                return;
            }

            $senha = password_hash($data['senha'], PASSWORD_DEFAULT);
            $tipo = isset($data['tipo']) ? $data['tipo'] : 'aluno';
            
            $stmt = $this->db->prepare("INSERT INTO users (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['nome'], $data['email'], $senha, $tipo]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Usuário criado com sucesso',
                'id' => $this->db->lastInsertId()
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }
    }

    public function buscar($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, tipo, status, created_at, updated_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuário não encontrado']);
                return;
            }

            echo json_encode(['success' => true, 'data' => $user]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
        }
    }

    public function atualizar($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['error' => 'Nenhum dado fornecido para atualização']);
                return;
            }

            $campos = [];
            $valores = [];
            
            if (isset($data['nome'])) {
                $campos[] = "nome = ?";
                $valores[] = $data['nome'];
            }
            
            if (isset($data['email'])) {
                // Verifica se o novo email já existe para outro usuário
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $id]);
                if ($stmt->fetch()) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email já cadastrado para outro usuário']);
                    return;
                }
                
                $campos[] = "email = ?";
                $valores[] = $data['email'];
            }
            
            if (isset($data['senha'])) {
                $campos[] = "senha = ?";
                $valores[] = password_hash($data['senha'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['tipo'])) {
                $campos[] = "tipo = ?";
                $valores[] = $data['tipo'];
            }
            
            if (isset($data['status'])) {
                $campos[] = "status = ?";
                $valores[] = $data['status'];
            }

            if (empty($campos)) {
                http_response_code(400);
                echo json_encode(['error' => 'Nenhum campo válido para atualização']);
                return;
            }

            $valores[] = $id;
            $sql = "UPDATE users SET " . implode(", ", $campos) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($valores);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuário não encontrado']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
        }
    }

    public function deletar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuário não encontrado']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Usuário deletado com sucesso']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar usuário: ' . $e->getMessage()]);
        }
    }
} 