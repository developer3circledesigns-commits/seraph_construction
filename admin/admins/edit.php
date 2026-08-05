<?php
/**
 * Admin — edit admin (super admin only).
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login.php');
Auth::requireRole($user, 'super_admin');

$id = (int)($_GET['id'] ?? 0);
$admin = Database::one('SELECT * FROM admins WHERE id = :id', [':id' => $id]);
if (!$admin) redirect('/admin/admins/index.php', 'Admin not found.', 'error');

$errors = [];
$old = $admin;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (!filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($body['full_name'])) $errors[] = 'Full name is required.';

    if (Database::scalar('SELECT 1 FROM admins WHERE email = :e AND id <> :id', [':e' => $body['email'], ':id' => $id])) {
        $errors[] = 'Another admin already uses this email.';
    }

    // Validate password reset BEFORE any update so we don't partially save.
    $newPassword = (string)($body['new_password'] ?? '');
    if ($newPassword !== '' && strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }

    // Self-protection: an admin cannot demote or deactivate their own account,
    // otherwise the system could be left without any active super admin.
    $isSelf = (int)$id === (int)$user['id'];
    $newRole = in_list($body['role'] ?? null, ['admin', 'super_admin']) ? $body['role'] : 'admin';
    if ($isSelf && $newRole !== $user['role']) {
        $errors[] = 'You cannot change your own role.';
    }
    if ($isSelf && empty($body['is_active']) && !empty($admin['is_active'])) {
        $errors[] = 'You cannot deactivate your own account.';
    }

    if (!$errors) {
        Database::execute(
            "UPDATE admins SET email = :email, full_name = :name, phone = :phone,
                    role = :role, is_active = :active WHERE id = :id",
            [
                ':email'  => $body['email'],
                ':name'   => $body['full_name'],
                ':phone'  => $body['phone'] ?? null,
                ':role'   => in_list($body['role'] ?? null, ['admin', 'super_admin']) ? $body['role'] : 'admin',
                ':active' => !empty($body['is_active']) ? 1 : 0,                ':id'     => $id,
            ]
        );

        if ($newPassword !== '' && !$errors) {
            Database::execute('UPDATE admins SET password_hash = :h WHERE id = :id', [
                ':h' => Auth::hash($newPassword), ':id' => $id,
            ]);
        }

        if (!$errors) {
            Audit::admin((int)$user['id'], 'admin_update', 'admin', $id);
            redirect('/admin/admins/index.php', 'Admin updated successfully.');
        }
    }
}

$title = 'Edit Admin';
$active = 'admins';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div><h1>Edit: <?php echo e($admin['full_name']); ?></h1></div>
</div>

<form method="POST" action="/admin/admins/edit.php?id=<?php echo (int)$id; ?>">
  <?php echo CSRF::field(); ?>
  <div class="card" style="max-width:560px">
    <div class="form-group">
      <label class="form-label" for="full_name">Full Name *</label>
      <input class="form-control" type="text" id="full_name" name="full_name" required value="<?php echo e($old['full_name']); ?>">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="email">Email *</label>
        <input class="form-control" type="email" id="email" name="email" required value="<?php echo e($old['email']); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone</label>
        <input class="form-control" type="text" id="phone" name="phone" value="<?php echo e($old['phone']); ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="new_password">Reset Password (blank to keep)</label>
      <input class="form-control" type="text" id="new_password" name="new_password" minlength="8" value="" autocomplete="off">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="role">Role</label>
        <select class="form-control" id="role" name="role">
          <option value="admin" <?php echo $old['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
          <option value="super_admin" <?php echo $old['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <label class="flex" style="cursor:pointer;gap:10px;padding-top:12px">
          <input type="checkbox" name="is_active" value="1" style="accent-color:var(--color-gold);width:16px;height:16px"
            <?php echo $old['is_active'] ? 'checked' : ''; ?>>
          <span>Active</span>
        </label>
      </div>
    </div>
  </div>
  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Save Changes</button>
    <a href="/admin/admins/index.php" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
