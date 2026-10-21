-- Créer la base de données
CREATE DATABASE IF NOT EXISTS frejsmithweb;
USE frejsmithweb;

-- Table pour les messages de contact
CREATE TABLE IF NOT EXISTS contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL
);

-- Table pour les avis des internautes
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les réservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    service VARCHAR(255) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour le suivi des visiteurs
CREATE TABLE IF NOT EXISTS visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    referrer VARCHAR(255),
    session_id VARCHAR(100),
    fingerprint TEXT,
    security_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les logs de sécurité
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

-- Table pour les erreurs JavaScript
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

-- Table pour les logs de consentement des utilisateurs
CREATE TABLE IF NOT EXISTS consent_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_name VARCHAR(255) NOT NULL,
    choice ENUM('accept', 'refuse', 'preferences') NOT NULL,
    page_url VARCHAR(500),
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les tentatives de connexion admin
CREATE TABLE IF NOT EXISTS admin_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(100),
    success BOOLEAN DEFAULT FALSE,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_contacts_email ON contacts(email);
CREATE INDEX idx_contacts_created_at ON contacts(created_at);
CREATE INDEX idx_reviews_created_at ON reviews(created_at);
CREATE INDEX idx_reservations_created_at ON reservations(created_at);
CREATE INDEX idx_visitors_created_at ON visitors(created_at);
CREATE INDEX idx_visitors_session_id ON visitors(session_id);
CREATE INDEX idx_visitors_ip ON visitors(ip_address);
CREATE INDEX idx_security_logs_created_at ON security_logs(created_at);
CREATE INDEX idx_security_logs_ip ON security_logs(ip_address);
CREATE INDEX idx_security_logs_severity ON security_logs(severity);
CREATE INDEX idx_js_errors_created_at ON js_errors(created_at);
CREATE INDEX idx_admin_attempts_ip ON admin_attempts(ip_address);
CREATE INDEX idx_admin_attempts_created_at ON admin_attempts(created_at);
CREATE INDEX idx_consent_logs_created_at ON consent_logs(created_at);
CREATE INDEX idx_consent_logs_choice ON consent_logs(choice);
CREATE INDEX idx_consent_logs_ip ON consent_logs(ip_address);
