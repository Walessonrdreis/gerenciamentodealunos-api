-- Inserir usuários iniciais para teste se não existirem
-- Nota: As senhas estão em formato hash (bcrypt) e são todas '123456'
-- O hash foi gerado com 10 rounds de salt

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