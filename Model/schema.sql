CREATE DATABASE IF NOT EXISTS projeto_final CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE projeto_final;

CREATE TABLE IF NOT EXISTS usuario (
 idusuario INT NOT NULL AUTO_INCREMENT,
 nome VARCHAR(150) NOT NULL,
 cpf VARCHAR(11) NOT NULL,
 dataNascimento DATE NULL,
 email VARCHAR(150) NULL,
 senha VARCHAR(255) NULL,
 PRIMARY KEY (idusuario),
 UNIQUE KEY cpf_UNIQUE (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS formacaoacademica (
 idformacaoAcademica INT NOT NULL AUTO_INCREMENT,
 idusuario INT NOT NULL,
 inicio DATE NULL,
 fim DATE NULL,
 descricao VARCHAR(150) NULL,
 PRIMARY KEY (idformacaoAcademica),
 KEY idusuario_idx (idusuario),
 CONSTRAINT fk_formacao_usuario FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
 ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS experienciaprofissional (
 idexperienciaprofissional INT NOT NULL AUTO_INCREMENT,
 idusuario INT NOT NULL,
 inicio DATE NULL,
 fim DATE NULL,
 empresa VARCHAR(45) NULL,
 descricao VARCHAR(45) NULL,
 PRIMARY KEY (idexperienciaprofissional),
 KEY idusuario_idx (idusuario),
 CONSTRAINT fk_experiencia_usuario FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
 ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS outrasformacoes (
 idoutrasformacoes INT NOT NULL AUTO_INCREMENT,
 idusuario INT NOT NULL,
 inicio DATE NULL DEFAULT NULL,
 fim DATE NULL DEFAULT NULL,
 descricao VARCHAR(150) NULL DEFAULT NULL,
 PRIMARY KEY (idoutrasformacoes),
 KEY idusuario_idx (idusuario),
 CONSTRAINT fk_outrasformacoes_usuario FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
 ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS administrador (
    idadministrador INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NULL,
    cpf VARCHAR(11) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    PRIMARY KEY (idadministrador),
    UNIQUE KEY administrador_cpf_UNIQUE (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajusta o tamanho dos campos de senha para armazenar os hashes.
ALTER TABLE usuario MODIFY senha VARCHAR(255) NULL;
ALTER TABLE administrador MODIFY senha VARCHAR(255) NOT NULL;

-- Insere o administrador inicial caso ele ainda não esteja cadastrado. A senha é atualizada para hash no primeiro acesso.
INSERT INTO administrador (nome, cpf, senha)
SELECT 'Bia', '22222222222', 'bia123'
WHERE NOT EXISTS (SELECT 1 FROM administrador WHERE cpf = '22222222222');
