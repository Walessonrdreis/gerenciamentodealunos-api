# Banco de Dados - Student Management System

## Visão Geral
Este é o esquema do banco de dados para o Sistema de Gerenciamento de Alunos. O banco foi projetado seguindo princípios de qualidade de software, incluindo SOLID, Clean Code e boas práticas de modelagem de dados.

## Princípios e Boas Práticas Aplicadas

### 1. Single Responsibility Principle (SRP)
- Cada tabela tem uma responsabilidade única e bem definida
- Separação clara entre diferentes tipos de dados
- Campos específicos para cada propósito

### 2. Normalização de Dados
- Tabelas normalizadas para evitar redundância
- Uso apropriado de chaves primárias e estrangeiras
- Relacionamentos bem definidos entre tabelas

### 3. Segurança
- Senhas armazenadas com hash
- Tokens de redefinição de senha com expiração
- Controle de status de usuário

### 4. Performance
- Índices estratégicos para otimização de consultas
- Tipos de dados apropriados para cada campo
- Constraints para garantir integridade dos dados

### 5. Manutenibilidade
- Comentários descritivos em tabelas e colunas
- Nomenclatura clara e consistente
- Versionamento de migrations

## Estrutura do Banco de Dados

### Tabela: users
Armazena informações dos usuários do sistema.

#### Colunas:
- `id` (VARCHAR(36)) - Identificador único do usuário (UUID)
- `name` (VARCHAR(100)) - Nome completo do usuário
- `email` (VARCHAR(100)) - Email do usuário (único)
- `password` (VARCHAR(255)) - Senha criptografada do usuário
- `role` (ENUM) - Função do usuário: admin, teacher, student
- `status` (ENUM) - Status do usuário: active, inactive
- `created_at` (TIMESTAMP) - Data de criação do registro
- `updated_at` (TIMESTAMP) - Data da última atualização
- `last_login` (TIMESTAMP) - Data do último login
- `reset_password_token` (VARCHAR(255)) - Token para redefinição de senha
- `reset_password_expires` (TIMESTAMP) - Expiração do token de redefinição

#### Índices:
- PRIMARY KEY em `id`
- UNIQUE KEY em `email`
- INDEX em `role` e `status` para otimização de consultas

## Como Usar

### Executar Migrações
Para criar/atualizar as tabelas do banco de dados:
```bash
php database/migrate.php
```

### Dados Iniciais
O sistema já vem com alguns usuários pré-configurados para teste:
- Admin: admin@escola.com / 123456
- Professor: professor@escola.com / 123456
- Aluno: aluno@escola.com / 123456

## Próximas Atualizações
1. Implementar tabelas relacionadas:
   - Turmas (classes)
   - Disciplinas (subjects)
   - Notas (grades)
   - Frequência (attendance)

2. Adicionar triggers para:
   - Auditoria de alterações
   - Validações automáticas

3. Implementar procedures para:
   - Relatórios
   - Operações em lote 