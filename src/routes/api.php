<?php

use App\Controllers\AuthController;

error_log("Carregando rotas...");

// Rotas de autenticação
$router->post('/auth/login', [new AuthController(), 'login']);
error_log("Rota POST /auth/login registrada"); 