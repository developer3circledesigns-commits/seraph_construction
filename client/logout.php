<?php
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

Auth::logout(Auth::CLIENT);
redirect('/client/login.php', 'You have been signed out.', 'info');
