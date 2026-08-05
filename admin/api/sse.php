<?php
/**
 * Admin — Server-Sent Events stream.
 * Channels: user channel (admin_N) for personal notifications + project channels.
 * Auth: via session cookie (same-origin EventSource sends cookies).
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::user(Auth::ADMIN);
if (!$user) {
    header('Content-Type: text/event-stream');
    echo ": unauthorized\n\n";
    exit;
}

// Determine which projects this admin watches
$isSuper = Auth::isSuper($user);
if ($isSuper) {
    $rows = Database::all('SELECT DISTINCT id FROM projects');
    $projectIds = array_column($rows, 'id');
} else {
    $rows = Database::all(
        'SELECT p.id FROM projects p JOIN admin_projects ap ON ap.project_id = p.id WHERE ap.admin_id = :aid',
        [':aid' => $user['id']]
    );
    $projectIds = array_column($rows, 'id');
}

$channels = [SSE::userChannel(Auth::ADMIN, (int)$user['id'])];
foreach ($projectIds as $pid) {
    $channels[] = SSE::projectChannel((int)$pid);
}

$lastEventId = trim((string)($_SERVER['HTTP_LAST_EVENT_ID'] ?? '0'));
SSE::stream($channels, $lastEventId);
