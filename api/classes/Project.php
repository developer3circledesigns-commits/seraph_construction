<?php
/**
 * SERAPH BUILD CONSTRUCTION — Project model.
 */

declare(strict_types=1);

class Project
{
    public const STATUSES = ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'];

    public const CATEGORIES = ['Residential', 'Commercial', 'Villa', 'Apartment', 'Office', 'Industrial'];

    private const LAYOUT_MAX_BYTES = 10 * 1024 * 1024; // 10MB

    public static function find(int $id): ?array
    {
        return Database::one(
            "SELECT p.*, c.company_name, c.contact_person, c.email AS client_email
               FROM projects p
               JOIN clients c ON c.id = p.client_id
              WHERE p.id = :id",
            [':id' => $id]
        );
    }

    public static function findForClient(int $id, int $clientId): ?array
    {
        return Database::one(
            "SELECT * FROM projects WHERE id = :id AND client_id = :cid",
            [':id' => $id, ':cid' => $clientId]
        );
    }

    public static function all(?array $filters = []): array
    {
        $sql = "SELECT p.*, c.company_name, c.contact_person,
                       (SELECT COUNT(*) FROM daily_updates u WHERE u.project_id = p.id) AS update_count
                  FROM projects p
                  JOIN clients c ON c.id = p.client_id";
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE :q1 OR c.company_name LIKE :q2 OR c.contact_person LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['client_id'])) {
            $where[] = 'p.client_id = :client_id';
            $params[':client_id'] = $filters['client_id'];
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY p.updated_at DESC';

        return Database::all($sql, $params);
    }

    /** Projects visible to a specific admin (super_admin sees all). */
    public static function allForAdmin(int $adminId, bool $isSuper, ?array $filters = []): array
    {
        if ($isSuper) {
            return self::all($filters);
        }

        $sql = "SELECT p.*, c.company_name, c.contact_person,
                       (SELECT COUNT(*) FROM daily_updates u WHERE u.project_id = p.id) AS update_count
                  FROM projects p
                  JOIN clients c ON c.id = p.client_id
                  JOIN admin_projects ap ON ap.project_id = p.id
                 WHERE ap.admin_id = :admin_id";
        $params = [':admin_id' => $adminId];

        if (!empty($filters['status'])) {
            $sql .= ' AND p.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (p.name LIKE :q1 OR c.company_name LIKE :q2 OR c.contact_person LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $filters['search'] . '%';
        }
        $sql .= ' ORDER BY p.updated_at DESC';

        return Database::all($sql, $params);
    }

    /** Projects visible to a client (only their own). */
    public static function allForClient(int $clientId): array
    {
        return Database::all(
            "SELECT p.*,
                    (SELECT COUNT(*) FROM daily_updates u WHERE u.project_id = p.id) AS update_count,
                    (SELECT MAX(update_date) FROM daily_updates u WHERE u.project_id = p.id) AS last_update_date
               FROM projects p
              WHERE p.client_id = :cid
              ORDER BY p.updated_at DESC",
            [':cid' => $clientId]
        );
    }

    /** Public-facing project list with layout info. */
    public static function publicList(): array
    {
        return Database::all(
            "SELECT p.id, p.name, p.category, p.description, p.location,
                    p.plot_size, p.built_up_area, p.floors, p.bedrooms, p.bathrooms,
                    p.style, p.status, p.progress_percentage, p.thumbnail,
                    (SELECT COUNT(*) FROM project_layouts pl WHERE pl.project_id = p.id) AS has_layout
               FROM projects p
              WHERE p.status IN ('in_progress', 'completed')
              ORDER BY p.status ASC, p.updated_at DESC"
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO projects
                (client_id, name, category, description, location,
                 plot_size, built_up_area, floors, bedrooms, bathrooms, style, thumbnail,
                 start_date, estimated_end_date,
                 status, progress_percentage, budget)
             VALUES
                (:client_id, :name, :category, :description, :location,
                 :plot_size, :built_up_area, :floors, :bedrooms, :bathrooms, :style, :thumbnail,
                 :start_date, :estimated_end_date,
                 :status, :progress_percentage, :budget)",
            [
                ':client_id'           => $data['client_id'],
                ':name'                => $data['name'],
                ':category'            => $data['category'] ?? null,
                ':description'         => $data['description'] ?? null,
                ':location'            => $data['location'] ?? null,
                ':plot_size'           => $data['plot_size'] ?? null,
                ':built_up_area'       => $data['built_up_area'] ?? null,
                ':floors'              => ($data['floors'] ?? '') !== '' ? (int)$data['floors'] : null,
                ':bedrooms'            => ($data['bedrooms'] ?? '') !== '' ? (int)$data['bedrooms'] : null,
                ':bathrooms'           => ($data['bathrooms'] ?? '') !== '' ? (int)$data['bathrooms'] : null,
                ':style'               => $data['style'] ?? null,
                ':thumbnail'           => $data['thumbnail'] ?? null,
                ':start_date'          => ($data['start_date'] ?? null) !== '' ? ($data['start_date'] ?? null) : null,
                ':estimated_end_date'  => ($data['estimated_end_date'] ?? null) !== '' ? ($data['estimated_end_date'] ?? null) : null,
                ':status'              => $data['status'] ?? 'planning',
                ':progress_percentage' => $data['progress_percentage'] ?? 0,
                ':budget'              => ($data['budget'] ?? null) !== '' ? ($data['budget'] ?? null) : null,
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::execute(
            "UPDATE projects SET
                client_id           = :client_id,
                name                = :name,
                category            = :category,
                description         = :description,
                location            = :location,
                plot_size           = :plot_size,
                built_up_area       = :built_up_area,
                floors              = :floors,
                bedrooms            = :bedrooms,
                bathrooms           = :bathrooms,
                style               = :style,
                thumbnail           = :thumbnail,
                start_date          = :start_date,
                estimated_end_date  = :estimated_end_date,
                actual_end_date     = :actual_end_date,
                status              = :status,
                progress_percentage = :progress_percentage,
                budget              = :budget
              WHERE id = :id",
            [
                ':client_id'           => $data['client_id'],
                ':name'                => $data['name'],
                ':category'            => $data['category'] ?? null,
                ':description'         => $data['description'] ?? null,
                ':location'            => $data['location'] ?? null,
                ':plot_size'           => $data['plot_size'] ?? null,
                ':built_up_area'       => $data['built_up_area'] ?? null,
                ':floors'              => ($data['floors'] ?? '') !== '' ? (int)$data['floors'] : null,
                ':bedrooms'            => ($data['bedrooms'] ?? '') !== '' ? (int)$data['bedrooms'] : null,
                ':bathrooms'           => ($data['bathrooms'] ?? '') !== '' ? (int)$data['bathrooms'] : null,
                ':style'               => $data['style'] ?? null,
                ':thumbnail'           => $data['thumbnail'] ?? null,
                ':start_date'          => ($data['start_date'] ?? null) !== '' ? ($data['start_date'] ?? null) : null,
                ':estimated_end_date'  => ($data['estimated_end_date'] ?? null) !== '' ? ($data['estimated_end_date'] ?? null) : null,
                ':actual_end_date'     => ($data['actual_end_date'] ?? null) !== '' ? ($data['actual_end_date'] ?? null) : null,
                ':status'              => $data['status'] ?? 'planning',
                ':progress_percentage' => $data['progress_percentage'] ?? 0,
                ':budget'              => ($data['budget'] ?? null) !== '' ? $data['budget'] : null,
                ':id'                  => $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM projects WHERE id = :id', [':id' => $id]);
    }

    /** Assign admins to a project (replaces existing assignments). */
    public static function assignAdmins(int $projectId, array $adminIds): void
    {
        Database::execute('DELETE FROM admin_projects WHERE project_id = :id', [':id' => $projectId]);

        foreach (array_unique($adminIds) as $adminId) {
            Database::execute(
                'INSERT IGNORE INTO admin_projects (admin_id, project_id) VALUES (:aid, :pid)',
                [':aid' => (int)$adminId, ':pid' => $projectId]
            );
        }
    }

    public static function assignedAdmins(int $projectId): array
    {
        return Database::all(
            "SELECT a.id, a.email, a.full_name, a.role
               FROM admins a
               JOIN admin_projects ap ON ap.admin_id = a.id
              WHERE ap.project_id = :id
              ORDER BY a.full_name",
            [':id' => $projectId]
        );
    }

    public static function stats(): array
    {
        return [
            'total'      => (int)Database::scalar('SELECT COUNT(*) FROM projects'),
            'active'     => (int)Database::scalar("SELECT COUNT(*) FROM projects WHERE status = 'in_progress'"),
            'on_hold'    => (int)Database::scalar("SELECT COUNT(*) FROM projects WHERE status = 'on_hold'"),
            'completed'  => (int)Database::scalar("SELECT COUNT(*) FROM projects WHERE status = 'completed'"),
            'planning'   => (int)Database::scalar("SELECT COUNT(*) FROM projects WHERE status = 'planning'"),
            'clients'    => (int)Database::scalar('SELECT COUNT(*) FROM clients'),
            'admins'     => (int)Database::scalar('SELECT COUNT(*) FROM admins'),
            'updates30'  => (int)Database::scalar('SELECT COUNT(*) FROM daily_updates WHERE update_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)'),
        ];
    }

    /** Get layout metadata for a project (without blob data). */
    public static function getLayout(int $projectId): ?array
    {
        return Database::one(
            'SELECT id, original_name, file_type, file_size, created_at
               FROM project_layouts WHERE project_id = :pid',
            [':pid' => $projectId]
        );
    }

    /** Upload a layout file for a project. Replaces existing layout. */
    public static function uploadLayout(int $projectId, array $file): void
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . $file['error']);
        }

        if (($file['size'] ?? 0) > self::LAYOUT_MAX_BYTES) {
            throw new RuntimeException('File exceeds the 10MB size limit.');
        }

        $data = file_get_contents((string)$file['tmp_name']);
        if ($data === false || $data === '') {
            throw new RuntimeException('Could not read the uploaded file.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file((string)$file['tmp_name']);

        $originalName = (string)($file['name'] ?? 'layout');
        $safeName     = self::safeLayoutName($originalName);

        self::deleteLayout($projectId);

        Database::insert(
            'INSERT INTO project_layouts (project_id, filename, original_name, file_type, file_size, file_data)
             VALUES (:pid, :fn, :on, :ft, :fs, :fd)',
            [
                ':pid' => $projectId,
                ':fn'  => $safeName,
                ':on'  => $originalName,
                ':ft'  => $mime,
                ':fs'  => strlen($data),
                ':fd'  => $data,
            ]
        );
    }

    /** Delete a project's layout file. */
    public static function deleteLayout(int $projectId): void
    {
        Database::execute(
            'DELETE FROM project_layouts WHERE project_id = :pid',
            [':pid' => $projectId]
        );
    }

    private static function safeLayoutName(string $original): string
    {
        $base = strtolower(pathinfo($original, PATHINFO_FILENAME));
        $ext  = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $base = preg_replace('/[^a-z0-9 _-]/', '', $base);
        $base = trim($base ?: 'layout');
        $base = substr($base, 0, 60);
        return $base . ($ext ? '.' . preg_replace('/[^a-z0-9]/', '', $ext) : '');
    }
}
