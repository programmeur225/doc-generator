-- ============================================
-- BASE DE DONNÉES : plateforme de génération de documents
-- ============================================

CREATE DATABASE IF NOT EXISTS doc_generator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doc_generator;

-- --------------------------------------------
-- Table: countries
-- --------------------------------------------
CREATE TABLE countries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(5) NOT NULL UNIQUE,        -- ex: CI, SN, ML (ISO 3166-1 alpha-2)
    flag_icon VARCHAR(255) NULL,            -- chemin/emoji du drapeau
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------
-- Table: document_types
-- --------------------------------------------
CREATE TABLE document_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    country_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,             -- ex: Attestation de travail
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_doctype_country FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_country_slug (country_id, slug)
) ENGINE=InnoDB;

ALTER TABLE document_types
ADD COLUMN price INT UNSIGNED NOT NULL DEFAULT 500 AFTER description;
ALTER TABLE document_types
ADD COLUMN custom_font_regular_path VARCHAR(500) NULL AFTER price,
ADD COLUMN custom_font_bold_path VARCHAR(500) NULL AFTER custom_font_regular_path;

-- --------------------------------------------
-- Table: document_versions
-- --------------------------------------------
CREATE TABLE document_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_type_id BIGINT UNSIGNED NOT NULL,
    version_number VARCHAR(20) NOT NULL,     -- ex: 1.0, 1.1, 2.0
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    notes TEXT NULL,                          -- notes internes sur la version
    published_at TIMESTAMP NULL DEFAULT NULL,
    created_by BIGINT UNSIGNED NULL,          -- FK vers users (admin créateur)
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_docversion_doctype FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_doctype_version (document_type_id, version_number)
) ENGINE=InnoDB;

-- --------------------------------------------
-- Table: document_pages
-- (chaque version peut avoir plusieurs pages/images)
-- --------------------------------------------
CREATE TABLE document_pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_version_id BIGINT UNSIGNED NOT NULL,
    page_number INT UNSIGNED NOT NULL DEFAULT 1,
    image_path VARCHAR(500) NOT NULL,         -- chemin du fichier image source
    image_width INT UNSIGNED NOT NULL,        -- largeur réelle en pixels
    image_height INT UNSIGNED NOT NULL,       -- hauteur réelle en pixels
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_docpage_version FOREIGN KEY (document_version_id) REFERENCES document_versions(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_version_page (document_version_id, page_number)
) ENGINE=InnoDB;

-- --------------------------------------------
-- Table: variables
-- (les champs positionnés sur les pages)
-- --------------------------------------------
CREATE TABLE variables (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_version_id BIGINT UNSIGNED NOT NULL,
    document_page_id BIGINT UNSIGNED NOT NULL,
    `key` VARCHAR(100) NOT NULL,               -- ex: nom_complet
    label VARCHAR(255) NOT NULL,               -- ex: Nom complet
    type ENUM('text', 'number', 'date', 'select', 'textarea', 'checkbox') NOT NULL DEFAULT 'text',
    is_required BOOLEAN NOT NULL DEFAULT TRUE,
    options JSON NULL,                          -- pour type = select : ["Option1","Option2"]
    placeholder VARCHAR(255) NULL,
    display_order INT UNSIGNED NOT NULL DEFAULT 0,

    -- Positionnement sur l'image (en % relatif à la taille de l'image, 0-100)
    position_x DECIMAL(6,3) NOT NULL,
    position_y DECIMAL(6,3) NOT NULL,
    box_width DECIMAL(6,3) NOT NULL,
    box_height DECIMAL(6,3) NOT NULL,

    -- Style du texte rendu
    font_family VARCHAR(100) NOT NULL DEFAULT 'DejaVu Sans',
    font_size INT UNSIGNED NOT NULL DEFAULT 14,
    font_color VARCHAR(7) NOT NULL DEFAULT '#000000',
    text_align ENUM('left', 'center', 'right') NOT NULL DEFAULT 'left',

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_variable_version FOREIGN KEY (document_version_id) REFERENCES document_versions(id) ON DELETE CASCADE,
    CONSTRAINT fk_variable_page FOREIGN KEY (document_page_id) REFERENCES document_pages(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_version_key (document_version_id, `key`)
) ENGINE=InnoDB;

ALTER TABLE variables
ADD COLUMN background_color VARCHAR(20) NOT NULL DEFAULT '#FFFFFF'
AFTER font_color;
ALTER TABLE variables
ADD COLUMN auto_font_size TINYINT(1) NOT NULL DEFAULT 1;
-- --------------------------------------------
-- Table: generated_documents
-- (historique des documents générés par les utilisateurs)
-- --------------------------------------------
CREATE TABLE generated_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_version_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,              -- nullable si génération publique/anonyme
    data JSON NOT NULL,                         -- valeurs saisies par l'utilisateur
    pdf_path VARCHAR(500) NULL,
    status ENUM('pending', 'generated', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_gendoc_version FOREIGN KEY (document_version_id) REFERENCES document_versions(id) ON DELETE CASCADE,
    CONSTRAINT fk_gendoc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
ALTER TABLE generated_documents
ADD COLUMN is_paid BOOLEAN NOT NULL DEFAULT FALSE AFTER status,
ADD COLUMN geniuspay_reference VARCHAR(100) NULL AFTER is_paid,
ADD COLUMN paid_at TIMESTAMP NULL DEFAULT NULL AFTER geniuspay_reference;
-- --------------------------------------------
-- Table: users (Laravel par défaut, étendue)
-- --------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------
-- Index utiles pour la performance
-- --------------------------------------------
CREATE INDEX idx_doctype_country ON document_types(country_id);
CREATE INDEX idx_docversion_status ON document_versions(status);
CREATE INDEX idx_variable_version ON variables(document_version_id);
CREATE INDEX idx_gendoc_user ON generated_documents(user_id);