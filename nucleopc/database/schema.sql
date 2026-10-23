-- =========================================================
-- NÚCLEO PC - Loja de Computadores e Peças
-- Script de criação do banco de dados
-- SGBD: MySQL (compatível com XAMPP / phpMyAdmin)
-- =========================================================

CREATE DATABASE IF NOT EXISTS nucleopc_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nucleopc_db;

-- ---------------------------------------------------------
-- Usuários internos (login do Painel Admin - loja física)
-- ---------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin','vendedor') NOT NULL DEFAULT 'vendedor',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    reset_token VARCHAR(64) NULL,
    reset_token_expira DATETIME NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Clientes (cadastro/login da Loja Online)
-- ---------------------------------------------------------
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14),
    telefone VARCHAR(20),
    cep VARCHAR(9),
    endereco VARCHAR(200),
    numero VARCHAR(10),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    uf CHAR(2),
    reset_token VARCHAR(64) NULL,
    reset_token_expira DATETIME NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Fornecedores
-- ---------------------------------------------------------
CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cnpj VARCHAR(20),
    telefone VARCHAR(20),
    email VARCHAR(150),
    cep VARCHAR(9),
    endereco VARCHAR(200),
    status ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Categorias de produtos
-- ---------------------------------------------------------
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descricao VARCHAR(255)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Produtos
-- ---------------------------------------------------------
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(30) NOT NULL UNIQUE,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    categoria_id INT,
    marca VARCHAR(80),
    preco DECIMAL(10,2) NOT NULL DEFAULT 0,
    custo DECIMAL(10,2) NOT NULL DEFAULT 0,
    estoque INT NOT NULL DEFAULT 0,
    estoque_minimo INT NOT NULL DEFAULT 3,
    imagem VARCHAR(255) DEFAULT 'sem-imagem.png',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Entradas de mercadoria (compras de fornecedores)
-- ---------------------------------------------------------
CREATE TABLE entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fornecedor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_entrada DATE NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pendente','confirmada') NOT NULL DEFAULT 'confirmada',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE entrada_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entrada_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    custo_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (entrada_id) REFERENCES entradas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Pedidos: usada tanto para venda de balcão (loja física)
-- quanto para pedidos feitos pelo cliente na loja online
-- ---------------------------------------------------------
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NULL,
    usuario_id INT NULL,
    origem ENUM('loja_fisica','online') NOT NULL DEFAULT 'online',
    status ENUM('aguardando_pagamento','confirmado','enviado','entregue','cancelado') NOT NULL DEFAULT 'confirmado',
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    endereco_entrega VARCHAR(255),
    forma_pagamento VARCHAR(50),
    preferencia_id VARCHAR(60) NULL,
    pagamento_id VARCHAR(60) NULL,
    pagamento_status VARCHAR(30) NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Nota fiscal (emitida a partir de um pedido/venda)
-- ---------------------------------------------------------
CREATE TABLE notas_fiscais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    numero INT NOT NULL,
    serie INT NOT NULL DEFAULT 1,
    data_emissao DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Mensagens de contato (formulário da loja online)
-- ---------------------------------------------------------
CREATE TABLE contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    assunto VARCHAR(150),
    mensagem TEXT NOT NULL,
    respondido TINYINT(1) NOT NULL DEFAULT 0,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- DADOS INICIAIS (SEED)
-- =========================================================

-- Usuário admin padrão -> senha: admin123
INSERT INTO usuarios (nome, email, senha, tipo) VALUES
('Administrador Núcleo PC', 'admin@nucleopc.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Vendedor Balcão', 'vendedor@nucleopc.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor');

INSERT INTO categorias (nome, slug, descricao) VALUES
('Processadores', 'processadores', 'CPUs para desktop e notebook'),
('Placas de Vídeo', 'placas-de-video', 'GPUs para jogos e trabalho gráfico'),
('Memórias RAM', 'memorias-ram', 'Módulos de memória DDR4 e DDR5'),
('Armazenamento', 'armazenamento', 'SSDs e HDs'),
('Placas-mãe', 'placas-mae', 'Motherboards para diversos sockets'),
('Periféricos', 'perifericos', 'Teclados, mouses e headsets');

INSERT INTO fornecedores (nome, cnpj, telefone, email, cep, endereco) VALUES
('Distribuidora TechParts LTDA', '12.345.678/0001-90', '(21) 3333-4444', 'contato@techparts.com.br', '26010-000', 'Av. Marechal Rondon, 500 - Centro - Nova Iguaçu/RJ'),
('HardCore Componentes S.A.', '98.765.432/0001-10', '(11) 4444-5555', 'vendas@hardcore.com.br', '01310-000', 'Av. Paulista, 1000 - Bela Vista - São Paulo/SP');

INSERT INTO produtos (sku, nome, descricao, categoria_id, marca, preco, custo, estoque, estoque_minimo, imagem) VALUES
('CPU-R5-5600', 'Processador AMD Ryzen 5 5600', '6 núcleos, 12 threads, clock de até 4.4GHz. Ótimo para jogos em Full HD.', 1, 'AMD', 899.90, 650.00, 15, 5, 'ryzen5600.png'),
('CPU-I5-13400F', 'Processador Intel Core i5-13400F', '10 núcleos (6P+4E), clock de até 4.6GHz.', 1, 'Intel', 1199.90, 900.00, 10, 5, 'i5-13400f.png'),
('GPU-RTX4060', 'Placa de Vídeo RTX 4060 8GB', 'Ray tracing e DLSS 3 para jogos em alta qualidade.', 2, 'NVIDIA', 2199.90, 1750.00, 8, 3, 'rtx4060.png'),
('GPU-RX7600', 'Placa de Vídeo RX 7600 8GB', 'Excelente custo-benefício para Full HD.', 2, 'AMD', 1899.90, 1500.00, 6, 3, 'rx7600.png'),
('RAM-16GB-3200', 'Memória RAM 16GB DDR4 3200MHz (Kit 2x8GB)', 'Kit dual channel para maior desempenho.', 3, 'Kingston', 249.90, 180.00, 30, 10, 'ram16gb.png'),
('SSD-NVME-1TB', 'SSD NVMe 1TB', 'Leitura de até 3500MB/s, interface PCIe.', 4, 'Kingston', 399.90, 290.00, 20, 8, 'ssd1tb.png'),
('MB-B550', 'Placa-mãe B550M Gaming', 'Socket AM4, suporte a DDR4 e PCIe 4.0.', 5, 'ASRock', 549.90, 400.00, 12, 5, 'b550.png'),
('TEC-MEC-RGB', 'Teclado Mecânico RGB', 'Switch blue, layout ABNT2, iluminação RGB.', 6, 'Redragon', 229.90, 140.00, 25, 10, 'teclado.png'),
('MOUSE-GAMER', 'Mouse Gamer 12000 DPI', '6 botões programáveis, sensor óptico de precisão.', 6, 'Redragon', 129.90, 75.00, 40, 10, 'mouse.png'),
('HEADSET-71', 'Headset Gamer 7.1 Surround', 'Som surround virtual e microfone retrátil.', 6, 'HyperX', 279.90, 190.00, 18, 6, 'headset.png');
