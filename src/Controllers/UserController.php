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
            $stmt = $this->db->query("SELECT id, name, email, role, status, created_at, updated_at FROM users");
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
            
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Name, email e password são obrigatórios']);
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

            // Validar role
            $roles_permitidos = ['admin', 'teacher', 'student'];
            $role = isset($data['role']) ? strtolower($data['role']) : 'student';
            if (!in_array($role, $roles_permitidos)) {
                http_response_code(400);
                echo json_encode(['error' => 'Role inválido. Valores permitidos: ' . implode(', ', $roles_permitidos)]);
                return;
            }

            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt->execute([$data['name'], $data['email'], $password, $role]);

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
            $stmt = $this->db->prepare("SELECT id, name, email, role, status, created_at, updated_at FROM users WHERE id = ?");
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
            
            if (isset($data['name'])) {
                $campos[] = "name = ?";
                $valores[] = $data['name'];
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
            
            if (isset($data['password'])) {
                $campos[] = "password = ?";
                $valores[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['role'])) {
                $roles_permitidos = ['admin', 'teacher', 'student'];
                $role = strtolower($data['role']);
                if (!in_array($role, $roles_permitidos)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Role inválido. Valores permitidos: ' . implode(', ', $roles_permitidos)]);
                    return;
                }
                $campos[] = "role = ?";
                $valores[] = $role;
            }
            
            if (isset($data['status'])) {
                $status_permitidos = ['active', 'inactive'];
                $status = strtolower($data['status']);
                if (!in_array($status, $status_permitidos)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Status inválido. Valores permitidos: ' . implode(', ', $status_permitidos)]);
                    return;
                }
                $campos[] = "status = ?";
                $valores[] = $status;
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