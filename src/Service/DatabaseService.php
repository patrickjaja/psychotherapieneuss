<?php
namespace App\Service;

use PDO;
use PDOException;

class DatabaseService
{
    private PDO $connection;

    public function __construct(
        string $host,
        string $dbname,
        string $username,
        string $password
    ) {
        // Handle environment variable placeholders
        if (strpos($host, 'env_') === 0) {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
        }
        if (strpos($dbname, 'env_') === 0) {
            $dbname = $_ENV['DB_NAME'] ?? '';
        }
        if (strpos($username, 'env_') === 0) {
            $username = $_ENV['DB_USER'] ?? '';
        }
        if (strpos($password, 'env_') === 0) {
            $password = $_ENV['DB_PASS'] ?? '';
        }
        
        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function createTables(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS contact_emails (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sender_name VARCHAR(255) NOT NULL,
                sender_email VARCHAR(255) NOT NULL,
                sender_phone VARCHAR(50) NOT NULL,
                appointment_timeframe VARCHAR(50),
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
        ";

        try {
            $this->connection->exec($sql);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to create tables: ' . $e->getMessage());
        }
    }

    public function saveContactEmail(array $data): int
    {
        $sql = "INSERT INTO contact_emails (sender_name, sender_email, sender_phone, appointment_timeframe, message, ip_address, user_agent) 
                VALUES (:name, :email, :phone, :appointment_timeframe, :message, :ip, :user_agent)";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'appointment_timeframe' => $data['appointment_timeframe'] ?? null,
            'message' => $data['message'],
            'ip' => $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function logEmail(array $data): void
    {
        $sql = "INSERT INTO email_log (contact_email_id, email_type, recipient_email, subject, body, status, sent_at, error_message) 
                VALUES (:contact_id, :type, :recipient, :subject, :body, :status, :sent_at, :error)";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'contact_id' => $data['contact_email_id'],
            'type' => $data['email_type'],
            'recipient' => $data['recipient_email'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => $data['status'] ?? 'pending',
            'sent_at' => $data['sent_at'] ?? null,
            'error' => $data['error_message'] ?? null
        ]);
    }

    public function updateContactEmailStatus(int $id, string $status): void
    {
        $sql = "UPDATE contact_emails SET status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getRecentSubmissionsCount(string $ipAddress, int $minutes = 60): int
    {
        $sql = "SELECT COUNT(*) as count FROM contact_emails 
                WHERE ip_address = :ip 
                AND created_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['ip' => $ipAddress, 'minutes' => $minutes]);
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }
}