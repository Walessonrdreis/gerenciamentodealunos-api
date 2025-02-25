<?php

use App\Controllers\AuthController;
use App\Controllers\AlunoController;
use App\Controllers\UserController;

// Rotas de autenticação
$router->post('/auth/login', [new AuthController(), 'login']);
$router->get('/auth/verify', [new AuthController(), 'verificarToken']);

// Rotas de usuários
$router->get('/users', [new UserController(), 'listar']);
$router->post('/users', [new UserController(), 'criar']);
$router->get('/users/{id}', [new UserController(), 'buscar']);
$router->put('/users/{id}', [new UserController(), 'atualizar']);
$router->delete('/users/{id}', [new UserController(), 'deletar']);

// Rotas de alunos
$router->get('/alunos', [new AlunoController(), 'listar']);
$router->post('/alunos', [new AlunoController(), 'criar']);
$router->get('/alunos/{id}', [new AlunoController(), 'buscar']);
$router->put('/alunos/{id}', [new AlunoController(), 'atualizar']);
$router->delete('/alunos/{id}', [new AlunoController(), 'deletar']);

// Rotas de Migração
$router->get('/migrations/run', function() {
    require_once __DIR__ . '/../../bin/run-migrations.php';
}); 