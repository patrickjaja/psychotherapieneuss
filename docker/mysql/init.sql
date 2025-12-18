-- Initialize database tables for psychotherapieneuss

USE psychotherapieneuss;

CREATE TABLE IF NOT EXISTS contact_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(255) NOT NULL,
    sender_email VARCHAR(255) NOT NULL,
    sender_phone VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    INDEX idx_created_at (created_at),
    INDEX idx_sender_email (sender_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_email_id INT,
    email_type ENUM('contact', 'confirmation') NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    sent_at TIMESTAMP NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_email_id) REFERENCES contact_emails(id) ON DELETE CASCADE,
    INDEX idx_contact_email_id (contact_email_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
