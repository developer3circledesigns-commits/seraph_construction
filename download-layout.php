<?php
/**
 * SERAPH BUILD CONSTRUCTION — Download project layout file.
 * Serves the uploaded layout file (PDF, image, etc.) from the database.
 * URL: /download-layout.php?id=PROJECT_ID
 *
 * Public access is limited to portfolio-visible projects only.
 */

declare(strict_types=1);
require __DIR__ . '/api/config/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    exit('Invalid project ID.');
}

$project = Database::one(
    'SELECT id, status FROM projects WHERE id = :id',
    [':id' => $id]
);

if (!$project || !in_array($project['status'], ['in_progress', 'completed'], true)) {
    http_response_code(404);
    exit('Layout not available for this project.');
}

$layout = Database::one(
    'SELECT id, original_name, file_type, file_size, file_data FROM project_layouts WHERE project_id = :pid',
    [':pid' => $id]
);

if (!$layout) {
    http_response_code(404);
    exit('No layout file found for this project.');
}

$filename = $layout['original_name'];
$fileType = $layout['file_type'];
$fileSize = (int)$layout['file_size'];
$fileData = $layout['file_data'];

if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: ' . $fileType);
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

echo $fileData;
exit;
