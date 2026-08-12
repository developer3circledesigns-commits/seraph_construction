<?php
/**
 * SERAPH BUILD CONSTRUCTION — Public contact / quote inquiries.
 */

declare(strict_types=1);

class ContactInquiry
{
    private static $schemaReady = false;

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

    /** @return array<int, array<string, mixed>> */
    public static function all(?string $search = null): array
    {
        self::ensureSchema();

        $params = [];
        $sql = 'SELECT *, DATE(created_at) AS query_date FROM contact_inquiries';

        if ($search !== null && $search !== '') {
            $sql .= ' WHERE full_name LIKE :q OR email LIKE :q OR phone LIKE :q OR service_type LIKE :q';
            $params[':q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC';

        return Database::all($sql, $params);
    }

    public static function find(int $id): ?array
    {
        self::ensureSchema();

        return Database::one('SELECT * FROM contact_inquiries WHERE id = :id', [':id' => $id]);
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
                ':service' => ($data['service_type'] ?? '') !== '' ? $data['service_type'] : null,
                ':message' => $data['message'],
                ':ip'      => $data['ip_address'],
            ]
        );
    }

    public static function serviceLabel(?string $type): string
    {
        if ($type === null || $type === '') {
            return '—';
        }

        $labels = [
            'construction'    => 'Construction',
            'interior'        => 'Interior Design',
            'modular_kitchen' => 'Modular Kitchen',
            'renovation'      => 'Renovation',
            'commercial'      => 'Commercial',
            'other'           => 'Other',
        ];

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}
