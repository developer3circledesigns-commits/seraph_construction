<?php
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

// Logout requires a POST with a valid CSRF token (prevents logout CSRF).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed — sign out using the button in the portal.');
}
CSRF::validate();

Auth::logout(Auth::CLIENT);
redirect('/client/login', 'You have been signed out.', 'info');
