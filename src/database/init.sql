USE tasks;

DROP TABLE IF EXISTS viacoes;

CREATE TABLE viacoes (
                         id               INT AUTO_INCREMENT PRIMARY KEY,
                         nome             VARCHAR(100)  NOT NULL,
                         url              VARCHAR(255)  NOT NULL DEFAULT '',
                         cidade           VARCHAR(100)  NOT NULL DEFAULT '',
                         logo             VARCHAR(255)  NULL,
                         status           TINYINT(1)    NOT NULL DEFAULT 1,
                         data_criacao     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         data_atualizacao TIMESTAMP     NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO viacoes (nome, url, cidade, status)
VALUES ('Cometa', 'https://queropassagem.com.br/auto-viacao-cometa', 'Curitiba', 1);