-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS bd_mundo;
USE bd_mundo;

-- Tabela de Países
CREATE TABLE IF NOT EXISTS paises (
    id_pais INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    continente VARCHAR(50) NOT NULL,
    populacao BIGINT NOT NULL,
    idioma VARCHAR(50) NOT NULL,
    bandeira VARCHAR(255),
    moeda VARCHAR(50),
    capital VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Cidades
CREATE TABLE IF NOT EXISTS cidades (
    id_cidade INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    populacao BIGINT NOT NULL,
    id_pais INT NOT NULL,
    temperatura DECIMAL(5, 2),
    descricao_clima VARCHAR(100),
    umidade INT,
    velocidade_vento DECIMAL(5, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pais) REFERENCES paises(id_pais) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unique_cidade_pais (nome, id_pais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para melhor performance
CREATE INDEX idx_pais_continente ON paises(continente);
CREATE INDEX idx_cidade_pais ON cidades(id_pais);