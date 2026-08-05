<?php
/**
 * Admin — create admin (super admin only).
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login.php');
Auth::requireRole($user, 'super_admin');

$errors = [];
$old = ['email' => '', 'full_name' => '', 'phone' => '', 'role' => 'admin', 'password' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (!filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($body['full_name'])) $errors[] = 'Full name is required.';
    if (strlen($body['password'] ?? '') < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!in_list($body['role'] ?? null, ['admin', 'super_admin'])) $errors[] = 'Invalid role.';

    if (Database::scalar('SELECT 1 FROM admins WHERE email = :e', [':e' => $body['email']])) {
        $errors[] = 'An admin with this email already exists.';
    }

    if (!$errors) {
        Database::insert(
            "INSERT INTO admins (email, password_hash, full_name, phone, role)
             VALUES (:email, :hash, :name, :phone, :role)",
            [
                ':email' => $body['email'],
                ':hash'  => Auth::hash($body['password']),
                ':name'  => $body['full_name'],
                ':phone' => $body['phone'] ?? null,
                ':role'  => $body['role'],
            ]
        );
        Audit::admin((int)$user['id'], 'admin_create', 'admin', null, ['email' => $body['email']]);
        redirect('/admin/admins/index.php', 'Admin account created.');
    }
}

$title = 'New Admin';
$active = 'admins';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div><h1>New Admin</h1><p>Create an admin account for your team.</p></div>
</div>

<form method="POST" action="/admin/admins/create.php">
  <?php echo CSRF::field(); ?>
  <div class="card" style="max-width:560px">
    <div class="form-group">
      <label class="form-label" for="full_name">Full Name *</label>
      <input class="form-control" type="text" id="full_name" name="full_name" required value="<?php echo e($old['full_name']); ?>">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="email">Email *</label>
        <input class="form-control" type="email" id="email" name="email" required value="<?php echo e($old['email']); ?>" autocomplete="off">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone</label>
        <input class="form-control" type="text" id="phone" name="phone" value="<?php echo e($old['phone']); ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="password">Password *</label>
      <input class="form-control" type="text" id="password" name="password" required minlength="8" value="<?php echo e($old['password']); ?>" autocomplete="off">
    </div>
    <div class="form-group">
      <label class="form-label" for="role">Role</label>
      <select class="form-control" id="role" name="role">
        <option value="admin" <?php echo $old['role'] === 'admin' ? 'selected' : ''; ?>>Admin — manages assigned projects</option>
        <option value="super_admin" <?php echo $old['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin — full control</option>
      </select>
      <div class="form-help">Super admins see all projects and can manage other admins.</div>
    </div>
  </div>
  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Create Admin</button>
    <a href="/admin/admins/index.php" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
