-- CRIAÇÃO DO BANCO

CREATE DATABASE IF NOT EXISTS tasks
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE tasks;

-- TABELA DE USUÁRIOS

CREATE TABLE IF NOT EXISTS users
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    email      VARCHAR(120) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    status     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NULL     DEFAULT NULL
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
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NULL     DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- TABELA DE HISTÓRICO DE VIAÇÕES
CREATE TABLE IF NOT EXISTS historico_viacoes
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    viacao_id  INT         NOT NULL,
    usuario_id INT         NOT NULL,
    acao       VARCHAR(50) NOT NULL,
    antes      JSON                 DEFAULT NULL,
    depois     JSON                 DEFAULT NULL,
    created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historico_viacao
        FOREIGN KEY (viacao_id)
            REFERENCES viacoes (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_historico_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES users (id)
            ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;