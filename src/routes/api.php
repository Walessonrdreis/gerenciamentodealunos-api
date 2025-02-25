<?php

use App\Controllers\AuthController;
use App\Controllers\AlunoController;

error_log("Carregando rotas...");

// Rotas de autenticação
$router->post('/auth/login', [new AuthController(), 'login']);
error_log("Rota POST /auth/login registrada");

// Rotas de alunos
$router->get('/alunos', [new AlunoController(), 'listar']);
$router->post('/alunos', [new AlunoController(), 'criar']);
$router->get('/alunos/{id}', [new AlunoController(), 'buscar']);
$router->put('/alunos/{id}', [new AlunoController(), 'atualizar']);
$router->delete('/alunos/{id}', [new AlunoController(), 'deletar']);

error_log("Rotas de alunos registradas"); 