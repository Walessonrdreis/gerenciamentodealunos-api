<?php

namespace App\Controllers;

use App\Core\Database\Database;

class AlunoController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM alunos");
            $alunos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $alunos]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao listar alunos: ' . $e->getMessage()]);
        }
    }

    public function criar() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nome']) || !isset($data['email'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Nome e email são obrigatórios']);
                return;
            }

            $stmt = $this->db->prepare("INSERT INTO alunos (nome, email) VALUES (?, ?)");
            $stmt->execute([$data['nome'], $data['email']]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Aluno criado com sucesso',
                'id' => $this->db->lastInsertId()
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar aluno: ' . $e->getMessage()]);
        }
    }

    public function buscar($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM alunos WHERE id = ?");
            $stmt->execute([$id]);
            $aluno = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$aluno) {
                http_response_code(404);
                echo json_encode(['error' => 'Aluno não encontrado']);
                return;
            }

            echo json_encode(['success' => true, 'data' => $aluno]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao buscar aluno: ' . $e->getMessage()]);
        }
    }

    public function atualizar($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nome']) || !isset($data['email'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Nome e email são obrigatórios']);
                return;
            }

            $stmt = $this->db->prepare("UPDATE alunos SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$data['nome'], $data['email'], $id]);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Aluno não encontrado']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Aluno atualizado com sucesso']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar aluno: ' . $e->getMessage()]);
        }
    }

    public function deletar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM alunos WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Aluno não encontrado']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Aluno deletado com sucesso']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar aluno: ' . $e->getMessage()]);
        }
    }
} 