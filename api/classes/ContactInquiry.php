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

    /** @return array{items: array<int, array<string, mixed>>, total: int, page: int, per_page: int, pages: int} */
    public static function paginated(?string $search = null, int $page = 1, int $perPage = 25): array
    {
        self::ensureSchema();

        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $params = [];
        $where = '';

        if ($search !== null && $search !== '') {
            $where = ' WHERE full_name LIKE :q1 OR email LIKE :q2 OR phone LIKE :q3 OR service_type LIKE :q4';
            $like = '%' . $search . '%';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
            $params[':q4'] = $like;
        }

        $total = (int)Database::scalar(
            'SELECT COUNT(*) FROM contact_inquiries' . $where,
            $params
        );

        $sql = 'SELECT *, DATE(created_at) AS query_date FROM contact_inquiries' . $where
            . ' ORDER BY created_at DESC LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset;

        $items = Database::all($sql, $params);
        $pages = $total > 0 ? (int)ceil($total / $perPage) : 1;

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => $pages,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(?string $search = null): array
    {
        return self::paginated($search, 1, 1000)['items'];
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
