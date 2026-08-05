<?php
/**
 * SERAPH BUILD CONSTRUCTION — File upload service.
 *
 * Safe image uploads:
 *  - Strict MIME + extension whitelist
 *  - Size limits (default 5MB)
 *  - Random UUID file names (no user-supplied names)
 *  - Stored under uploads/updates (outside web-adjacent PHP dirs)
 */

declare(strict_types=1);

class Uploader
{
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    private const MAX_BYTES = 5 * 1024 * 1024; // 5MB

    /**
     * Process an uploaded file and move it into the target directory.
     * Returns the stored relative path, or throws on error.
     *
     * @param array  $file   A $_FILES['name'] entry
     * @param string $targetDir e.g. ROOT_PATH . '/uploads/updates'
     * @param string $webPath   e.g. '/uploads/updates'
     */
    public static function store(array $file, string $targetDir, string $webPath): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('No file uploaded.');
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . $file['error']);
        }

        if ($file['size'] > self::MAX_BYTES) {
            throw new RuntimeException('File exceeds the 5MB size limit.');
        }

        $finfo   = new finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($file['tmp_name']);
        $ext     = self::ALLOWED_MIME[$mime] ?? null;

        if ($ext === null) {
            throw new RuntimeException('Only JPG, PNG, WEBP and GIF images are allowed.');
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Could not save the uploaded file.');
        }

        return rtrim($webPath, '/') . '/' . $name;
    }

    /**
     * Process multiple files from a multi-upload input.
     * Returns array of stored paths.
     */
    public static function storeMany(?array $files, string $targetDir, string $webPath): array
    {
        if (empty($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $stored = [];
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
                $stored[] = self::store($file, $targetDir, $webPath);
            } catch (RuntimeException $e) {
                // Skip invalid files in a multi-upload; caller can check count.
            }
        }

        return $stored;
    }
}
