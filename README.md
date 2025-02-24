# Gerenciamento de Alunos - API

API REST para o sistema de gerenciamento de alunos.

## Tecnologias
- PHP 8.1+
- MySQL
- Composer

## Instalação
1. Clone o repositório
2. Execute `composer install`
3. Copie `.env.example` para `.env` e configure
4. Execute `php database/migrate.php`

## Rotas
- POST /auth/login - Login
- GET /users - Listar usuários
- POST /users - Criar usuário
- PUT /users/{id} - Atualizar usuário
- DELETE /users/{id} - Deletar usuário
