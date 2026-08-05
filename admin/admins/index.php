<?php
/**
 * Admin — manage admins (super admin only).
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login.php');
Auth::requireRole($user, 'super_admin');

$admins = Database::all(
    "SELECT a.*, (SELECT COUNT(*) FROM admin_projects ap WHERE ap.admin_id = a.id) AS project_count
       FROM admins a
      ORDER BY a.created_at DESC"
);

$title = 'Admins';
$active = 'admins';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1>Admins</h1>
    <p><?php echo count($admins); ?> admin account(s).</p>
  </div>
  <a href="/admin/admins/create.php" class="btn btn--primary"><i class="fa-solid fa-user-shield"></i> New Admin</a>
</div>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Admin</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Projects</th>
        <th>Last Login</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($admins as $a): ?>
      <tr>
        <td><strong><?php echo e($a['full_name']); ?></strong></td>
        <td class="small"><?php echo e($a['email']); ?></td>
        <td class="small muted"><?php echo e($a['phone'] ?: '—'); ?></td>
        <td>
          <span class="badge <?php echo $a['role'] === 'super_admin' ? 'badge--milestone' : ''; ?>"><?php echo e($a['role']); ?></span>
        </td>
        <td class="muted"><?php echo (int)$a['project_count']; ?></td>
        <td class="small muted"><?php echo $a['last_login_at'] ? time_ago($a['last_login_at']) : 'Never'; ?></td>
        <td><span class="badge <?php echo $a['is_active'] ? 'badge--in_progress' : 'badge--cancelled'; ?>"><?php echo $a['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
        <td style="white-space:nowrap">
          <?php if ((int)$a['id'] !== (int)$user['id']): ?>
            <a class="btn btn--secondary btn--sm" href="/admin/admins/edit.php?id=<?php echo (int)$a['id']; ?>" aria-label="Edit admin <?php echo e($a['full_name']); ?>"><i class="fa-solid fa-pen"></i> Edit</a>
          <?php else: ?>
            <span class="muted small">(you)</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
