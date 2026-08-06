<?php
/**
 * SERAPH BUILD CONSTRUCTION — Image storage service.
 *
 * Daily-update photos are stored as binary blobs in the update_images
 * table (not in an uploads/ folder). Safe uploads:
 *  - Strict MIME whitelist (finfo sniffing, no extension trust)
 *  - Size limits (default 5MB per image)
 *  - Bytes are read from the temp file and stored as LONGBLOB
 */

declare(strict_types=1);

class Image
{
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    private const MAX_BYTES = 5 * 1024 * 1024; // 5MB

    /**
     * Process an uploaded file, returning its metadata + bytes
     * ready to persist. Throws RuntimeException on error.
     *
     * @param array $file A $_FILES['images'][] entry
     */
    public static function prepare(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('No file uploaded.');
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . $file['error']);
        }

        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new RuntimeException('File exceeds the 5MB size limit.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file((string)$file['tmp_name']);
        $ext   = self::ALLOWED_MIME[$mime] ?? null;

        if ($ext === null) {
            throw new RuntimeException('Only JPG, PNG, WEBP and GIF images are allowed.');
        }

        $data = file_get_contents((string)$file['tmp_name']);
        if ($data === false || $data === '') {
            throw new RuntimeException('Could not read the uploaded file.');
        }

        return [
            'file_name' => self::safeName((string)($file['name'] ?? ''), $ext),
            'mime_type' => $mime,
            'size'      => strlen($data),
            'data'      => $data,
        ];
    }

    /**
     * Persist a prepared image for a daily update.
     * Returns the new image id.
     */
    public static function store(array $prepared, int $updateId): int
    {
        return Database::insert(
            'INSERT INTO update_images (update_id, file_name, mime_type, size, data)
             VALUES (:uid, :fn, :mt, :sz, :data)',
            [
                ':uid'  => $updateId,
                ':fn'   => $prepared['file_name'],
                ':mt'   => $prepared['mime_type'],
                ':sz'   => $prepared['size'],
                ':data' => $prepared['data'],
            ]
        );
    }

    /**
     * Process + persist several files for an update.
     * Returns array of new image ids (invalid files are skipped).
     *
     * @param array|null $files A $_FILES['images'] structure
     */
    public static function storeMany(?array $files, int $updateId): array
    {
        if (empty($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $ids = [];
        foreach ($files['name'] as $i => $name) {
            if ($name === '') {
                continue;
            }
            $file = [
                'name'     => $name,
                'type'     => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error'    => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size'     => $files['size'][$i] ?? 0,
            ];
            try {
                $ids[] = self::store(self::prepare($file), $updateId);
            } catch (RuntimeException $e) {
                // Skip invalid files in a multi-upload; caller can check count.
            }
        }

        return $ids;
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM update_images WHERE id = :id', [':id' => $id]);
    }

    public static function findWithProject(int $id): ?array
    {
        return Database::one(
            "SELECT i.*, u.project_id
               FROM update_images i
               JOIN daily_updates u ON u.id = i.update_id
              WHERE i.id = :id",
            [':id' => $id]
        );
    }

    /**
     * Delete images by id (cleanup on edit/remove).
     */
    public static function deleteMany(array $ids): void
    {
        $ids = array_values(array_unique(array_filter($ids, fn($v) => (int)$v > 0)));
        if (empty($ids)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        Database::execute('DELETE FROM update_images WHERE id IN (' . $placeholders . ')', $ids);
    }

    /** Build a safe, extension-suffixed file name. */
    private static function safeName(string $original, string $ext): string
    {
        $base = strtolower(pathinfo($original, PATHINFO_FILENAME));
        // strip anything that is not a letter/number/dash/underscore/space
        $base = preg_replace('/[^a-z0-9 _-]/', '', $base);
        $base = trim($base ?: 'image');
        return substr($base, 0, 60) . '.' . $ext;
    }
}