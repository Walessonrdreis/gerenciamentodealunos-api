-- Configurações de produção
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Criação da tabela de usuários com configurações otimizadas para produção
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    reset_password_token VARCHAR(255) NULL,
    reset_password_expires TIMESTAMP NULL,
    CONSTRAINT users_email_unique UNIQUE (email)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  ROW_FORMAT=DYNAMIC;

-- Índices otimizados para produção (verificando se existem antes de criar)
DROP INDEX IF EXISTS idx_users_email ON users;
CREATE INDEX idx_users_email ON users(email);

DROP INDEX IF EXISTS idx_users_role_status ON users;
CREATE INDEX idx_users_role_status ON users(role, status);

-- Comentários da tabela e colunas para documentação
ALTER TABLE users 
    COMMENT 'Tabela para armazenar informações dos usuários do sistema';

ALTER TABLE users
    MODIFY COLUMN id VARCHAR(36) COMMENT 'Identificador único do usuário (UUID)',
    MODIFY COLUMN name VARCHAR(100) COMMENT 'Nome completo do usuário',
    MODIFY COLUMN email VARCHAR(100) COMMENT 'Email do usuário (único)',
    MODIFY COLUMN password VARCHAR(255) COMMENT 'Senha criptografada do usuário',
    MODIFY COLUMN role ENUM('admin', 'teacher', 'student') COMMENT 'Função do usuário no sistema',
    MODIFY COLUMN status ENUM('active', 'inactive') COMMENT 'Status atual do usuário',
    MODIFY COLUMN created_at TIMESTAMP COMMENT 'Data e hora de criação do registro',
    MODIFY COLUMN updated_at TIMESTAMP COMMENT 'Data e hora da última atualização',
    MODIFY COLUMN last_login TIMESTAMP COMMENT 'Data e hora do último login',
    MODIFY COLUMN reset_password_token VARCHAR(255) COMMENT 'Token para redefinição de senha',
    MODIFY COLUMN reset_password_expires TIMESTAMP COMMENT 'Data de expiração do token de redefinição de senha';

-- Reativar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 1; 