-- Remover tabela de migrações se existir
DROP TABLE IF EXISTS migrations;

-- Criar tabela de migrações
CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    data_nascimento DATE,
    cpf VARCHAR(14) UNIQUE,
    telefone VARCHAR(20),
    endereco TEXT,
    cidade VARCHAR(100),
    estado CHAR(2),
    cep VARCHAR(9),
    status ENUM('ativo', 'inativo', 'trancado') NOT NULL DEFAULT 'ativo',
    observacoes TEXT,
    user_id VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário admin se não existir
INSERT IGNORE INTO users (id, name, email, password, role, status) VALUES
(
    'a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6',
    'Administrador',
    'admin@escola.com',
    '$2a$10$JqB.MOOXzGNZoAP/CGt44.0IqhxKz7KPYqrfwQm1.SYR0qJWyXE5W',
    'admin',
    'active'
),
(
    'b2c3d4e5-f6g7-h8i9-j0k1-l2m3n4o5p6q7',
    'Professor Exemplo',
    'professor@escola.com',
    '$2a$10$JqB.MOOXzGNZoAP/CGt44.0IqhxKz7KPYqrfwQm1.SYR0qJWyXE5W',
    'teacher',
    'active'
),
(
    'c3d4e5f6-g7h8-i9j0-k1l2-m3n4o5p6q7r8',
    'Aluno Exemplo',
    'aluno@escola.com',
    '$2a$10$JqB.MOOXzGNZoAP/CGt44.0IqhxKz7KPYqrfwQm1.SYR0qJWyXE5W',
    'student',
    'active'
);

-- Inserir alunos de exemplo
INSERT IGNORE INTO alunos (nome, email, data_nascimento, cpf, telefone, cidade, estado) VALUES 
('João da Silva', 'joao@escola.com', '2000-01-01', '123.456.789-00', '(11) 98765-4321', 'São Paulo', 'SP'),
('Maria Santos', 'maria@escola.com', '2001-02-02', '987.654.321-00', '(11) 91234-5678', 'Rio de Janeiro', 'RJ');

-- Registrar migrações executadas
INSERT INTO migrations (migration) VALUES 
('ResetMigrations'),
('CreateUsersTable'),
('CreateAlunosTable'),
('AddNewAdmin'),
('UpdateAdminPassword'); 