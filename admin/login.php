<?php
/**
 * SERAPH BUILD CONSTRUCTION — Admin login.
 */

declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

// Already logged in? Redirect to dashboard.
if (Auth::user(Auth::ADMIN)) {
    redirect('/admin/');
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body  = request_body();
    $email = trim((string)($body['email'] ?? ''));
    $pass  = (string)($body['password'] ?? '');

    // Rate limiting (shared + per identifier)
    $lockSeconds = RateLimiter::lockedOut($email);
    if ($lockSeconds > 0) {
        $errors[] = 'Too many failed attempts. Please try again in ' . ceil($lockSeconds / 60) . ' minutes.';
    } elseif ($email === '' || $pass === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $user = Auth::login(Auth::ADMIN, $email, $pass);
        if ($user) {
            RateLimiter::recordSuccess($email);
            Audit::admin((int)$user['id'], 'login');
            redirect('/admin/', 'Welcome back, ' . $user['full_name'] . '!');
        }
        RateLimiter::attempt($email);
        $errors[] = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Seraph Build Construction</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="/css/panel/base.css">
  <link rel="stylesheet" href="/css/panel/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="auth-body auth-body--admin">
  <div class="auth-card">
    <div class="auth-card__brand">
      <img src="/images/seraph-logo@204w.webp" alt="Seraph Build Construction" height="40">
      <div>
        <span class="auth-card__label"><i class="fa-solid fa-shield-halved"></i> Admin Panel</span>
        <h1>Sign in to the Admin Panel</h1>
        <p>Manage projects, clients and daily construction updates.</p>
      </div>
    </div>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert--error"><?php echo e($err); ?></div>
    <?php endforeach; ?>
    <?php echo flash(); ?>

    <form method="POST" action="/admin/login.php" novalidate>
      <div class="form-group">
        <label class="form-label" for="email">Email address</label>
        <input class="form-control" type="email" id="email" name="email"
               value="<?php echo e($email); ?>" required autocomplete="username"
               placeholder="admin@seraphbuild.com" autofocus>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password"
               required autocomplete="current-password" placeholder="••••••••">
      </div>
      <?php echo CSRF::field(); ?>
      <button type="submit" class="btn btn--primary btn--block">Sign In</button>
    </form>

    <p class="auth-back"><a href="/">← Back to website</a></p>
  </div>

  <div class="auth-footer">&copy; <?php echo date('Y'); ?> Seraph Build Construction. Authorized personnel only.</div>
</body>
</html>
