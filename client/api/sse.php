<?php
/**
 * Client portal — Server-Sent Events stream.
 * Subscribes to all projects belonging to the client.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::user(Auth::CLIENT);
if (!$user) {
    header('Content-Type: text/event-stream');
    echo ": unauthorized\n\n";
    exit;
}

$rows = Database::all(
    'SELECT id FROM projects WHERE client_id = :cid',
    [':cid' => $user['id']]
);

$channels = [SSE::userChannel(Auth::CLIENT, (int)$user['id'])];
foreach ($rows as $r) {
    $channels[] = SSE::projectChannel((int)$r['id']);
}

$lastEventId = trim((string)($_SERVER['HTTP_LAST_EVENT_ID'] ?? '0'));
SSE::stream($channels, $lastEventId);
