<?php
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::user(Auth::ADMIN);
if ($user) {
    Audit::admin((int)$user['id'], 'logout');
}
Auth::logout(Auth::ADMIN);
redirect('/admin/login', 'You have been signed out.', 'info');
