<?php
/**
 * SERAPH BUILD CONSTRUCTION — Serve a stored image from the database.
 *
 * URL: /image?id=N
 * Auth required. Access is restricted to:
 *  - any active admin (super or project-assigned)
 *  - the client who owns the image's project
 *
 * Images are stored as LONGBLOB rows (see update_images table), not files.
 */

declare(strict_types=1);
require __DIR__ . '/api/config/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    exit;
}

// Determine which session is active (admin or client)
$admin  = Auth::user(Auth::ADMIN);
$client = Auth::user(Auth::CLIENT);

if (!$admin && !$client) {
    http_response_code(401);
    exit;
}

$image = Image::findWithProject($id);
if (!$image) {
    http_response_code(404);
    exit;
}

$allowed = false;
if ($admin) {
    if (Auth::isSuper($admin)) {
        $allowed = true;
    } else {
        $assigned = array_column(Project::assignedAdmins((int)$image['project_id']), 'id');
        $allowed  = in_array((int)$admin['id'], $assigned, true);
    }
} elseif ($client) {
    $project = Project::findForClient((int)$image['project_id'], (int)$client['id']);
    $allowed = $project !== null;
}

if (!$allowed) {
    http_response_code(403);
    exit;
}

header('Content-Type: ' . $image['mime_type']);
header('Content-Length: ' . (int)$image['size']);
header('Cache-Control: private, max-age=86400');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="' . addslashes($image['file_name']) . '"');
echo $image['data'];
exit;