<?php
/**
 * Admin — notifications API (AJAX).
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::user(Auth::ADMIN);
if (!$user) json_error('Unauthorized', 401);

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
    case 'list':
        json_success([
            'unread'    => Notification::unreadCount('admin', (int)$user['id']),
            'items'     => Notification::forUser('admin', (int)$user['id']),
        ]);
        break;

    case 'read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
        CSRF::validate();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        Notification::markRead('admin', (int)$user['id'], $id);
        json_success(['unread' => Notification::unreadCount('admin', (int)$user['id'])]);
        break;

    case 'read_all':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
        CSRF::validate();
        Notification::markRead('admin', (int)$user['id']);
        json_success(['unread' => 0]);
        break;

    default:
        json_error('Unknown action', 400);
}