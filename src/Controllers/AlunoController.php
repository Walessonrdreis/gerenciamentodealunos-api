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
            $stmt = $this->db->query("SELECT * FROM alunos ORDER BY nome");
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

            // Verifica se o email já existe
            $stmt = $this->db->prepare("SELECT id FROM alunos WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Email já cadastrado']);
                return;
            }

            // Verifica se o CPF já existe (se fornecido)
            if (!empty($data['cpf'])) {
                $stmt = $this->db->prepare("SELECT id FROM alunos WHERE cpf = ?");
                $stmt->execute([$data['cpf']]);
                if ($stmt->fetch()) {
                    http_response_code(400);
                    echo json_encode(['error' => 'CPF já cadastrado']);
                    return;
                }
            }

            $campos = ['nome', 'email'];
            $valores = [$data['nome'], $data['email']];
            $placeholders = ['?', '?'];

            $campos_opcionais = [
                'data_nascimento', 'cpf', 'telefone', 'endereco',
                'cidade', 'estado', 'cep', 'status', 'observacoes', 'user_id'
            ];

            foreach ($campos_opcionais as $campo) {
                if (isset($data[$campo])) {
                    $campos[] = $campo;
                    $valores[] = $data[$campo];
                    $placeholders[] = '?';
                }
            }

            $sql = "INSERT INTO alunos (" . implode(", ", $campos) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($valores);

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
            
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['error' => 'Nenhum dado fornecido para atualização']);
                return;
            }

            $campos = [];
            $valores = [];

            // Campos que podem ser atualizados
            $campos_permitidos = [
                'nome', 'email', 'data_nascimento', 'cpf', 'telefone',
                'endereco', 'cidade', 'estado', 'cep', 'status',
                'observacoes', 'user_id'
            ];

            foreach ($campos_permitidos as $campo) {
                if (isset($data[$campo])) {
                    // Verificar email único
                    if ($campo === 'email') {
                        $stmt = $this->db->prepare("SELECT id FROM alunos WHERE email = ? AND id != ?");
                        $stmt->execute([$data['email'], $id]);
                        if ($stmt->fetch()) {
                            http_response_code(400);
                            echo json_encode(['error' => 'Email já cadastrado para outro aluno']);
                            return;
                        }
                    }

                    // Verificar CPF único
                    if ($campo === 'cpf' && !empty($data['cpf'])) {
                        $stmt = $this->db->prepare("SELECT id FROM alunos WHERE cpf = ? AND id != ?");
                        $stmt->execute([$data['cpf'], $id]);
                        if ($stmt->fetch()) {
                            http_response_code(400);
                            echo json_encode(['error' => 'CPF já cadastrado para outro aluno']);
                            return;
                        }
                    }

                    $campos[] = "$campo = ?";
                    $valores[] = $data[$campo];
                }
            }

            if (empty($campos)) {
                http_response_code(400);
                echo json_encode(['error' => 'Nenhum campo válido para atualização']);
                return;
            }

            $valores[] = $id;
            $sql = "UPDATE alunos SET " . implode(", ", $campos) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($valores);

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