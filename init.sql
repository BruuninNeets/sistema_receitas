CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    situacao ENUM('ativo', 'inativo') DEFAULT 'ativo'
);

CREATE TABLE IF NOT EXISTS receita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    custo DECIMAL(10,2),
    tipo_receita ENUM('doce', 'salgada')
);

INSERT INTO usuario (nome, login, senha, situacao) 
VALUES ('Administrador', 'admin', '123456', 'ativo');