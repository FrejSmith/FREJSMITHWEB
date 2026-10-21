-- Script de mise à jour de la base de données pour ajouter les fonctionnalités de sécurité
-- À exécuter après avoir appliqué le nouveau schema.sql

USE frejsmithweb;

-- Ajouter les nouvelles colonnes à la table visitors si elles n'existent pas
ALTER TABLE visitors
ADD COLUMN IF NOT EXISTS session_id VARCHAR(100) AFTER referrer,
ADD COLUMN IF NOT EXISTS fingerprint TEXT AFTER session_id,
ADD COLUMN IF NOT EXISTS security_data JSON AFTER fingerprint;

-- Créer les nouvelles tables si elles n'existent pas
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(100),
    success BOOLEAN DEFAULT FALSE,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Créer les index pour les performances
CREATE INDEX IF NOT EXISTS idx_visitors_session_id ON visitors(session_id);
CREATE INDEX IF NOT EXISTS idx_visitors_ip ON visitors(ip_address);
CREATE INDEX IF NOT EXISTS idx_security_logs_created_at ON security_logs(created_at);
CREATE INDEX IF NOT EXISTS idx_security_logs_ip ON security_logs(ip_address);
CREATE INDEX IF NOT EXISTS idx_security_logs_severity ON security_logs(severity);
CREATE INDEX IF NOT EXISTS idx_js_errors_created_at ON js_errors(created_at);
CREATE INDEX IF NOT EXISTS idx_admin_attempts_ip ON admin_attempts(ip_address);
CREATE INDEX IF NOT EXISTS idx_admin_attempts_created_at ON admin_attempts(created_at);

-- Message de confirmation
SELECT 'Mise à jour de sécurité terminée avec succès !' as status;