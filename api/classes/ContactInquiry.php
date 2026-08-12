<?php
/**
 * SERAPH BUILD CONSTRUCTION — Public contact / quote inquiries.
 */

declare(strict_types=1);

class ContactInquiry
{
    private static bool $schemaReady = false;

    /** Create the inquiries table on first use (shared-hosting friendly). */
    public static function ensureSchema(): void
    {
        if (self::$schemaReady) {
            return;
        }

        Database::execute(
            "CREATE TABLE IF NOT EXISTS contact_inquiries (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                full_name       VARCHAR(120) NOT NULL,
                email           VARCHAR(255) NOT NULL,
                phone           VARCHAR(30) NOT NULL,
                service_type    VARCHAR(80) DEFAULT NULL,
                message         TEXT NOT NULL,
                ip_address      VARCHAR(45) DEFAULT NULL,
                created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_contact_created (created_at),
                INDEX idx_contact_ip (ip_address, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::$schemaReady = true;
    }

    public static function recentCountByIp(string $ip): int
    {
        self::ensureSchema();

        return (int)Database::scalar(
            "SELECT COUNT(*) FROM contact_inquiries
              WHERE ip_address = :ip AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [':ip' => $ip]
        );
    }

    public static function create(array $data): int
    {
        self::ensureSchema();

        return Database::insert(
            "INSERT INTO contact_inquiries (full_name, email, phone, service_type, message, ip_address)
             VALUES (:name, :email, :phone, :service, :message, :ip)",
            [
                ':name'    => $data['full_name'],
                ':email'   => $data['email'],
                ':phone'   => $data['phone'],
                ':service' => $data['service_type'] !== '' ? $data['service_type'] : null,
                ':message' => $data['message'],
                ':ip'      => $data['ip_address'],
            ]
        );
    }
}
