-- ==================================================
-- DATABASE InfuseMe: E-commerce Tè, Tisane e Infusi
-- ==================================================
-- Imposta il charset UTF-8
SET NAMES utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_database = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_server = utf8mb4;

-- Crea il database se non esiste già
CREATE DATABASE IF NOT EXISTS db_InfuseMe 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE db_InfuseMe;

-- Prima le tabelle che referenziano altre tabelle
DROP TABLE IF EXISTS add_prodotto;
DROP TABLE IF EXISTS add_base;
DROP TABLE IF EXISTS add_ingrediente;
DROP TABLE IF EXISTS visualizza_ordine;
DROP TABLE IF EXISTS dettaglio_ordine;
DROP TABLE IF EXISTS ordine;
DROP TABLE IF EXISTS custom_ingrediente;
DROP TABLE IF EXISTS prodotto_custom;
DROP TABLE IF EXISTS prodotto_ingrediente;
DROP TABLE IF EXISTS prodotto;
DROP TABLE IF EXISTS ingrediente;
DROP TABLE IF EXISTS base;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS admin;

-- ----------------------------------------------------------------
-- TABELLA ADMIN
-- ----------------------------------------------------------------
CREATE TABLE admin (
    id_admin INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    data_creazione DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Amministratori del sistema';

-- ----------------------------------------------------------------
-- TABELLA UTENTE
-- ----------------------------------------------------------------
CREATE TABLE utente (
    id_utente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    data_nascita DATE NOT NULL,
    indirizzo VARCHAR(255) NULL,
    citta VARCHAR(100) NULL,
    cap VARCHAR(20) NULL,
    paese VARCHAR(100) NULL DEFAULT 'Italia',
    data_registrazione DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Clienti registrati';

-- ----------------------------------------------------------------
-- TABELLA BASE
-- ----------------------------------------------------------------
CREATE TABLE base (
    id_base INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL UNIQUE,
    descrizione TEXT NULL,
    img_path VARCHAR(255) NULL,
    temperatura_infusione VARCHAR(50) NOT NULL,
    tempo_infusione VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- TABELLA INGREDIENTE
-- ----------------------------------------------------------------
CREATE TABLE ingrediente (
    id_ingrediente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(150) NOT NULL UNIQUE,
    descrizione TEXT NULL,
    tipo ENUM('frutto', 'spezia', 'fiore', 'dolcificante', 'note') NOT NULL,
    img_path VARCHAR(255) NULL,
    disponibile INT UNSIGNED NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- TABELLA PRODOTTO
-- ----------------------------------------------------------------
CREATE TABLE prodotto (
    id_prodotto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL UNIQUE,
    descrizione TEXT NOT NULL,
    prezzo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    grammi INT NULL DEFAULT 50,
    categoria ENUM('tè_verde', 'tè_nero', 'tè_bianco', 'tè_giallo', 'tè_oolong', 'tisana', 'infuso', 'altro') NOT NULL,
    img_path VARCHAR(255) NULL,
    disponibilita INT NOT NULL DEFAULT 200,
    ultima_modifica DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_base INT UNSIGNED NULL,
    FOREIGN KEY (id_base) REFERENCES base(id_base) ON DELETE RESTRICT,
    CONSTRAINT chk_prezzo_positivo CHECK (prezzo >= 0),
    CONSTRAINT chk_grammi_positivi CHECK (grammi > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- PRODOTTO_CUSTOM
-- ----------------------------------------------------------------
CREATE TABLE prodotto_custom (
    id_custom INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_blend VARCHAR(200) NOT NULL DEFAULT 'Il Tuo Blend',
    descrizione TEXT NULL,
    num_ingredienti INT UNSIGNED NOT NULL DEFAULT 2,
    grammi INT NULL DEFAULT 50,
    prezzo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    data_creazione DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_base INT UNSIGNED NOT NULL,
    FOREIGN KEY (id_base) REFERENCES base(id_base) ON DELETE RESTRICT,
    CONSTRAINT chk_custom_num_ingredienti CHECK (num_ingredienti BETWEEN 2 AND 5),
    CONSTRAINT chk_custom_prezzo CHECK (prezzo >= 0),
    CONSTRAINT chk_custom_grammi CHECK (grammi > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- RELAZIONE M:N tra PRODOTTO e INGREDIENTE
-- ----------------------------------------------------------------
CREATE TABLE prodotto_ingrediente (
    id_prodotto INT UNSIGNED NOT NULL,
    id_ingrediente INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_prodotto, id_ingrediente),
    FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto) ON DELETE CASCADE,
    FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id_ingrediente) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- RELAZIONE M:N tra PRODOTTO_CUSTOM e INGREDIENTE
-- ----------------------------------------------------------------
CREATE TABLE custom_ingrediente (
    id_custom INT UNSIGNED NOT NULL,
    id_ingrediente INT UNSIGNED NOT NULL,
    posizione SMALLINT UNSIGNED NULL,
    PRIMARY KEY (id_custom, id_ingrediente),
    FOREIGN KEY (id_custom) REFERENCES prodotto_custom(id_custom) ON DELETE CASCADE,
    FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id_ingrediente) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- ORDINE
-- ----------------------------------------------------------------
CREATE TABLE ordine (
    id_ordine INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utente INT UNSIGNED NOT NULL,
    data_ordine DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_consegna DATE NULL,
    indirizzo_spedizione VARCHAR(255) NULL,
    stato_ord ENUM('in_attesa','pagato','in_preparazione','spedito','consegnato','annullato') NOT NULL DEFAULT 'in_attesa',
    conferma_pagamento BOOLEAN NOT NULL DEFAULT FALSE,
    sottototale DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    spese_spedizione DECIMAL(10,2) NOT NULL DEFAULT 4.99,
    totale DECIMAL(10,2) NOT NULL,
    omaggio BOOLEAN NOT NULL DEFAULT FALSE,
    descrizione_omaggio VARCHAR(255) NULL,
    note TEXT NULL,
    FOREIGN KEY (id_utente) REFERENCES utente(id_utente) ON DELETE RESTRICT,
    CONSTRAINT chk_ordine_totale CHECK (totale >= 0),
    CONSTRAINT chk_ordine_sottototale CHECK (sottototale >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- DETTAGLIO_ORDINE
-- ----------------------------------------------------------------
CREATE TABLE dettaglio_ordine (
    id_dettaglio INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_ordine INT UNSIGNED NOT NULL,
    id_prodotto INT UNSIGNED NULL,
    id_custom INT UNSIGNED NULL,
    quantita INT UNSIGNED NOT NULL DEFAULT 1,
    prezzo_unit DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    totale_riga DECIMAL(10,2) GENERATED ALWAYS AS (quantita * prezzo_unit) VIRTUAL,
    note VARCHAR(255) NULL,
    FOREIGN KEY (id_ordine) REFERENCES ordine(id_ordine) ON DELETE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto) ON DELETE RESTRICT,
    FOREIGN KEY (id_custom) REFERENCES prodotto_custom(id_custom) ON DELETE RESTRICT,
    CONSTRAINT chk_dettaglio_one_product CHECK (
        (id_prodotto IS NOT NULL AND id_custom IS NULL)
        OR
        (id_prodotto IS NULL AND id_custom IS NOT NULL)
    ),
    CONSTRAINT chk_dettaglio_quantita CHECK (quantita > 0),
    CONSTRAINT chk_dettaglio_prezzo CHECK (prezzo_unit >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Dettagli righe ordine';

-- ================================================================
-- TABELLE DI TRACCIAMENTO ADMIN
-- ================================================================
CREATE TABLE visualizza_ordine (
    id_admin INT UNSIGNED NOT NULL,
    id_ordine INT UNSIGNED NOT NULL,
    data_azione DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin, id_ordine),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE CASCADE,
    FOREIGN KEY (id_ordine) REFERENCES ordine(id_ordine) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log visualizzazioni ordini';

CREATE TABLE add_ingrediente (
    id_admin INT UNSIGNED NOT NULL,
    id_ingrediente INT UNSIGNED NOT NULL,
    data_modifica DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin, id_ingrediente),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE CASCADE,
    FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id_ingrediente) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log modifiche ingredienti';

CREATE TABLE add_base (
    id_admin INT UNSIGNED NOT NULL,
    id_base INT UNSIGNED NOT NULL,
    data_modifica DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin, id_base),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE RESTRICT,
    FOREIGN KEY (id_base) REFERENCES base(id_base) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log modifiche basi';

CREATE TABLE add_prodotto (
    id_admin INT UNSIGNED NOT NULL,
    id_prodotto INT UNSIGNED NOT NULL,
    data_modifica DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin, id_prodotto),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE RESTRICT,
    FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log modifiche prodotti';

-- ----------------------------------------------------------------
-- INDICI
-- ----------------------------------------------------------------
CREATE INDEX idx_det_ordine ON dettaglio_ordine(id_ordine);
CREATE INDEX idx_det_prodotto ON dettaglio_ordine(id_prodotto);
CREATE INDEX idx_det_custom ON dettaglio_ordine(id_custom);
CREATE INDEX idx_prodotto_nome ON prodotto(nome);
CREATE INDEX idx_ingrediente_nome ON ingrediente(nome);
CREATE INDEX idx_base_nome ON base(nome);