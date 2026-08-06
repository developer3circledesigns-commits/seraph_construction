<?php
/**
 * Admin — activity / audit log.
 */
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$logs = Audit::recent(200);

$title = 'Activity Log';
$active = 'audit';
include __DIR__ . '/partials/header.php';
?>
<div class="page-header">
  <div>
    <h1>Activity Log</h1>
    <p>Recent actions taken by administrators.</p>
  </div>
</div>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>When</th>
        <th>Admin ID</th>
        <th>Action</th>
        <th>Entity</th>
        <th>IP Address</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($logs)): ?>
      <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-clipboard"></i>No activity recorded yet.</div></td></tr>
    <?php endif; ?>
    <?php foreach ($logs as $l): ?>
      <tr>
        <td class="small muted"><?php echo e(date('d M Y H:i', strtotime($l['created_at']))); ?></td>
        <td class="small"><?php echo $l['actor_id'] ? (int)$l['actor_id'] : '—'; ?></td>
        <td><span class="badge" style="background:var(--color-surface-2);color:var(--color-text-muted)"><?php echo e($l['action']); ?></span></td>
        <td class="small muted"><?php echo e($l['entity'] ?: '—'); ?><?php echo $l['entity_id'] ? ' #' . (int)$l['entity_id'] : ''; ?></td>
        <td class="small muted"><?php echo e($l['ip_address']); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
