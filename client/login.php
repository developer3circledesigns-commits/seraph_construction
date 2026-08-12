<?php
/**
 * SERAPH BUILD CONSTRUCTION — Client portal login.
 */

declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

if (Auth::user(Auth::CLIENT)) {
    redirect('/client/');
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body  = request_body();
    $email = trim((string)($body['email'] ?? ''));
    $pass  = (string)($body['password'] ?? '');

    // Login CSRF protection + rate limiting (shared + per identifier)
    if (!CSRF::verify(is_string($body['_csrf'] ?? null) ? $body['_csrf'] : '')) {
        $errors[] = 'Your session has expired. Please refresh the page and try again.';
    } elseif (($lockSeconds = RateLimiter::lockedOut($email)) > 0) {
        $errors[] = 'Too many failed attempts. Please try again in ' . ceil($lockSeconds / 60) . ' minutes.';
    } elseif ($email === '' || $pass === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $user = Auth::login(Auth::CLIENT, $email, $pass);
        if ($user) {
            RateLimiter::recordSuccess($email);
            redirect('/client/', 'Welcome back, ' . $user['contact_person'] . '!');
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
  <title>Client Portal Login — Seraph Build Construction</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <link rel="stylesheet" href="/css/panel/base.css">
  <link rel="stylesheet" href="/css/panel/auth.css">
  <link rel="stylesheet" href="/css/panel/fa.css">
  <noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..600;1,9..144,400..600&family=IBM+Plex+Mono:wght@400;500&family=Public+Sans:ital,wght@0,400..700;1,400..700&display=swap">
  </noscript>
</head>
<body class="auth-body">
  <div class="auth-card">
    <div class="auth-card__brand">
      <img src="/images/seraph-logo@204w.webp" alt="Seraph Build Construction" width="170" height="40">
      <div>
        <span class="auth-card__label"><i class="fa-solid fa-user-check"></i> Client Portal</span>
        <h1>Track your construction project</h1>
        <p>Sign in to view daily updates on your project.</p>
      </div>
    </div>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert--error"><?php echo e($err); ?></div>
    <?php endforeach; ?>
    <?php echo flash(); ?>

    <form method="POST" action="/client/login" novalidate>
      <div class="form-group">
        <label class="form-label" for="email">Email address</label>
        <input class="form-control" type="email" id="email" name="email"
               value="<?php echo e($email); ?>" required autocomplete="username"
               placeholder="you@example.com" autofocus>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="input-wrapper">
          <input class="form-control" type="password" id="password" name="password"
                 required autocomplete="current-password" placeholder="••••••••">
          <button type="button" class="toggle-password" onclick="togglePassword(this, 'password')" aria-label="Show password">
            <i class="fa-solid fa-eye" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <?php echo CSRF::field(); ?>
      <button type="submit" class="btn btn--primary btn--block">Sign In</button>
    </form>

    <p class="auth-back"><a href="/">← Back to website</a></p>
  </div>

  <div class="auth-footer">&copy; <?php echo date('Y'); ?> Seraph Build Construction. Authorized clients only.</div>

  <script defer src="/js/async-css.js"></script>
  <script>
  function togglePassword(element, inputId) {
    var input = document.getElementById(inputId);
    if (input.type === 'password') {
      input.type = 'text';
      // Change eye to eye-slash icon
      var eyeIcon = element.querySelector('i');
      if (eyeIcon) {
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    } else {
      input.type = 'password';
      // Change eye-slash back to eye icon
      var eyeIcon = element.querySelector('i');
      if (eyeIcon) {
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }
  }
  </script>
</body>
</html>
