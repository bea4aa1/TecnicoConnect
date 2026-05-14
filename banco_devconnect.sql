CREATE DATABASE IF NOT EXISTS tecnicoconnect
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tecnicoconnect;

CREATE TABLE usuarios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(120)  NOT NULL,
  email         VARCHAR(120)  NOT NULL UNIQUE,
  senha         VARCHAR(255)  NOT NULL,
  tipo          ENUM('dev','empresa') NOT NULL DEFAULT 'dev',
  especialidade VARCHAR(60)   DEFAULT NULL,
  nivel         VARCHAR(40)   DEFAULT NULL,
  titulo        VARCHAR(120)  DEFAULT NULL,
  bio           TEXT          DEFAULT NULL,
  cidade        VARCHAR(80)   DEFAULT NULL,
  modelo        VARCHAR(30)   DEFAULT NULL,
  github        VARCHAR(200)  DEFAULT NULL,
  linkedin      VARCHAR(200)  DEFAULT NULL,
  portfolio     VARCHAR(200)  DEFAULT NULL,
  telefone      VARCHAR(20)   DEFAULT NULL,
  email_contato VARCHAR(120)  DEFAULT NULL,
  photo_url     TEXT          DEFAULT NULL,
  segmento      VARCHAR(60)   DEFAULT NULL,
  tamanho       VARCHAR(40)   DEFAULT NULL,
  site          VARCHAR(200)  DEFAULT NULL,
  about         TEXT          DEFAULT NULL,
  cover_url     TEXT          DEFAULT NULL,
  score         INT           DEFAULT 0,
  trust_score   INT           DEFAULT 0,
  ativo         TINYINT(1)    DEFAULT 1,
  criado_em     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE habilidades (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT         NOT NULL,
  nome       VARCHAR(80) NOT NULL,
  nivel      VARCHAR(30) NOT NULL,
  categoria  VARCHAR(60) DEFAULT NULL,
  criado_em  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  UNIQUE KEY uq_usuario_hab (usuario_id, nome)
);

CREATE TABLE experiencias (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id   INT          NOT NULL,
  cargo        VARCHAR(120) NOT NULL,
  empresa      VARCHAR(120) NOT NULL,
  emp_linkedin VARCHAR(200) DEFAULT NULL,
  inicio       DATE         NOT NULL,
  fim          DATE         DEFAULT NULL,
  descricao    TEXT         DEFAULT NULL,
  criado_em    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE projetos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT          NOT NULL,
  nome       VARCHAR(120) NOT NULL,
  descricao  TEXT         DEFAULT NULL,
  stack      VARCHAR(300) DEFAULT NULL,
  github     VARCHAR(200) DEFAULT NULL,
  demo       VARCHAR(200) DEFAULT NULL,
  criado_em  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE certificacoes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT          NOT NULL,
  nome        VARCHAR(150) NOT NULL,
  instituicao VARCHAR(100) DEFAULT NULL,
  ano         YEAR         DEFAULT NULL,
  horas       VARCHAR(20)  DEFAULT NULL,
  url         VARCHAR(300) DEFAULT NULL,
  criado_em   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE idiomas (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT        NOT NULL,
  idioma     VARCHAR(50) NOT NULL,
  nivel      VARCHAR(50) NOT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  UNIQUE KEY uq_usuario_idioma (usuario_id, idioma)
);

CREATE TABLE vagas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  empresa_id    INT          NOT NULL,
  titulo        VARCHAR(150) NOT NULL,
  especialidade VARCHAR(60)  NOT NULL,
  nivel         VARCHAR(30)  NOT NULL,
  stack         VARCHAR(400) DEFAULT NULL,
  descricao     TEXT         DEFAULT NULL,
  salario       VARCHAR(60)  DEFAULT NULL,
  modelo        VARCHAR(30)  DEFAULT NULL,
  local         VARCHAR(80)  DEFAULT NULL,
  ativa         TINYINT(1)   DEFAULT 1,
  criado_em     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (empresa_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE candidaturas (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT        NOT NULL,
  vaga_id    INT        NOT NULL,
  score      VARCHAR(10) DEFAULT '0%',
  status     ENUM('em-analise','aprovado','recusado') DEFAULT 'em-analise',
  criado_em  TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (vaga_id)    REFERENCES vagas(id)    ON DELETE CASCADE,
  UNIQUE KEY uq_cand (usuario_id, vaga_id)
);

CREATE TABLE posts (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT  NOT NULL,
  texto      TEXT NOT NULL,
  tags       VARCHAR(300) DEFAULT NULL,
  likes      INT          DEFAULT 0,
  criado_em  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE mensagens (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  de_id     INT  NOT NULL,
  para_id   INT  NOT NULL,
  texto     TEXT NOT NULL,
  lida      TINYINT(1) DEFAULT 0,
  criado_em TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (de_id)   REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (para_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE conexoes (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  de_id     INT NOT NULL,
  para_id   INT NOT NULL,
  status    ENUM('pendente','aceita','recusada') DEFAULT 'pendente',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (de_id)   REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (para_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  UNIQUE KEY uq_conexao (de_id, para_id)
);


CREATE INDEX idx_hab_uid   ON habilidades(usuario_id);
CREATE INDEX idx_exp_uid   ON experiencias(usuario_id);
CREATE INDEX idx_proj_uid  ON projetos(usuario_id);
CREATE INDEX idx_cert_uid  ON certificacoes(usuario_id);
CREATE INDEX idx_vaga_emp  ON vagas(empresa_id);
CREATE INDEX idx_vaga_ativa ON vagas(ativa);
CREATE INDEX idx_cand_vaga ON candidaturas(vaga_id);
CREATE INDEX idx_cand_uid  ON candidaturas(usuario_id);
CREATE INDEX idx_post_uid  ON posts(usuario_id);
CREATE INDEX idx_msg_de    ON mensagens(de_id);
CREATE INDEX idx_msg_para  ON mensagens(para_id);
CREATE INDEX idx_conn_de   ON conexoes(de_id);
CREATE INDEX idx_conn_para ON conexoes(para_id);
