-- CRIAÇÃO DO BANCO

CREATE DATABASE IF NOT EXISTS viacoes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE viacoes;

-- TABELA DE USUÁRIOS

CREATE TABLE IF NOT EXISTS usuarios
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    email      VARCHAR(120) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    status     TINYINT(1)   NOT NULL DEFAULT 1,
    data_criacao TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP    NULL     DEFAULT NULL
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- TABELA DE VIAÇÕES
CREATE TABLE IF NOT EXISTS viacoes
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    url        VARCHAR(255) NOT NULL DEFAULT '',
    cidade     VARCHAR(100) NOT NULL DEFAULT '',
    logo       VARCHAR(255)          DEFAULT NULL,
    status     TINYINT(1)   NOT NULL DEFAULT 1,
    data_criacao TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP    NULL     DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- TABELA DE HISTÓRICO DE VIAÇÕES
CREATE TABLE IF NOT EXISTS historico_viacoes
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    viacao_id    INT          NOT NULL,
    usuario_id   INT          NOT NULL,
    acao         VARCHAR(50)  NOT NULL,
    dados        JSON         NOT NULL,
    data_criacao TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_viacao_id  (viacao_id),
    INDEX idx_usuario_id (usuario_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;