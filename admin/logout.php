<?php
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

// Logout requires a POST with a valid CSRF token (prevents logout CSRF).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed — sign out using the button in the panel.');
}
CSRF::validate();

$user = Auth::user(Auth::ADMIN);
if ($user) {
    Audit::admin((int)$user['id'], 'logout');
}
Auth::logout(Auth::ADMIN);
redirect('/admin/login', 'You have been signed out.', 'info');
