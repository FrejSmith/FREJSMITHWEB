-- Script de mise à jour de la base de données pour ajouter les fonctionnalités de sécurité
USE frejsmithweb;

-- Ajouter les colonnes manquantes à visitors
SET @session_id_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='visitors' AND COLUMN_NAME='session_id' AND TABLE_SCHEMA='frejsmithweb');
SET @fingerprint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='visitors' AND COLUMN_NAME='fingerprint' AND TABLE_SCHEMA='frejsmithweb');
SET @security_data_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='visitors' AND COLUMN_NAME='security_data' AND TABLE_SCHEMA='frejsmithweb');

ALTER TABLE visitors ADD COLUMN session_id VARCHAR(100) AFTER referrer;
ALTER TABLE visitors ADD COLUMN fingerprint TEXT AFTER session_id;
ALTER TABLE visitors ADD COLUMN security_data JSON AFTER fingerprint;

-- Créer la table security_logs
CREATE TABLE IF NOT EXISTS security_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(100) NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    session_id VARCHAR(100),
    fingerprint TEXT,
    page_url VARCHAR(500),
    event_data JSON,
    threat_score INT DEFAULT 0,
    blocked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_severity (severity),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer la table js_errors
CREATE TABLE IF NOT EXISTS js_errors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message TEXT NOT NULL,
    filename VARCHAR(500),
    lineno INT,
    colno INT,
    user_agent TEXT,
    url VARCHAR(500),
    session_id VARCHAR(100),
    fingerprint TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer la table admin_attempts
CREATE TABLE IF NOT EXISTS admin_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(100),
    success BOOLEAN DEFAULT FALSE,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (ip_address),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajouter les index supplémentaires si nécessaire
CREATE INDEX IF NOT EXISTS idx_visitors_session_id ON visitors(session_id);
CREATE INDEX IF NOT EXISTS idx_visitors_ip ON visitors(ip_address);

-- Vérification
SELECT 'Mise à jour de sécurité appliquée avec succès!' as status;
SELECT 'Tables actuelles:' as check_header;
SHOW TABLES LIKE '%';
