# Documentação da API

Base URL: `https://seoeads.com/api`

## Autenticação

### Login
```http
POST /auth/login
Content-Type: application/json

{
    "email": "admin@escola.com",
    "password": "123456"
}
```

Resposta de sucesso:
```json
{
    "success": true,
    "message": "Login realizado com sucesso",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
        "id": "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
        "name": "Administrador",
        "email": "admin@escola.com",
        "role": "admin"
    }
}
```

### Verificar Token
```http
GET /auth/verify
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## Usuários

### Listar Usuários
```http
GET /users
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Criar Usuário
```http
POST /users
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
    "name": "Novo Usuário",
    "email": "novo@escola.com",
    "password": "123456",
    "role": "teacher"
}
```

### Buscar Usuário
```http
GET /users/{id}
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Atualizar Usuário
```http
PUT /users/{id}
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
    "name": "Nome Atualizado",
    "email": "atualizado@escola.com",
    "role": "teacher"
}
```

### Deletar Usuário
```http
DELETE /users/{id}
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## Alunos

### Listar Alunos
```http
GET /alunos
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Criar Aluno
```http
POST /alunos
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
    "name": "Novo Aluno",
    "email": "aluno@escola.com",
    "matricula": "2025001"
}
```

### Buscar Aluno
```http
GET /alunos/{id}
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Atualizar Aluno
```http
PUT /alunos/{id}
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
    "name": "Nome Atualizado",
    "email": "atualizado@escola.com",
    "matricula": "2025001"
}
```

### Deletar Aluno
```http
DELETE /alunos/{id}
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## Observações

1. Todas as requisições (exceto login) devem incluir o token JWT no header `Authorization`
2. O token tem validade de 24 horas
3. Roles disponíveis: `admin`, `teacher`, `student`
4. Respostas de erro seguem o formato:
```json
{
    "success": false,
    "error": "Mensagem de erro"
}
```
5. Códigos de status HTTP:
   - 200: Sucesso
   - 400: Erro de validação
   - 401: Não autorizado
   - 403: Acesso negado
   - 404: Recurso não encontrado
   - 500: Erro interno do servidor 