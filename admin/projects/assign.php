<?php
/**
 * Admin — assign admins to a project.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login.php');

$id = (int)($_GET['id'] ?? 0);
$project = Project::find($id);
if (!$project) redirect('/admin/projects/index.php', 'Project not found.', 'error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $adminIds = isset($body['admin_ids']) ? array_map('intval', (array)$body['admin_ids']) : [];
    Project::assignAdmins($id, $adminIds);
    Audit::admin((int)$user['id'], 'project_assign', 'project', $id, ['admins' => $adminIds]);
    redirect('/admin/projects/view.php?id=' . $id, 'Admin assignments updated.');
}

$admins = Database::all("SELECT id, full_name, email, role FROM admins WHERE is_active = 1 ORDER BY full_name");
$assigned = array_column(Project::assignedAdmins($id), 'id');

$title = 'Assign Admins';
$active = 'projects';
include dirname(__DIR__) . '/partials/header.php';
?>
<div class="page-header">
  <div>
    <h1>Assign Admins</h1>
    <p><?php echo e($project['name']); ?> — choose which admins manage this project.</p>
  </div>
</div>

<form method="POST" action="/admin/projects/assign.php?id=<?php echo (int)$id; ?>">
  <?php echo CSRF::field(); ?>
  <div class="card" style="max-width:560px">
    <div class="checklist">
      <?php foreach ($admins as $a): ?>
        <label>
          <input type="checkbox" name="admin_ids[]" value="<?php echo (int)$a['id']; ?>"
            <?php echo in_array($a['id'], $assigned) ? 'checked' : ''; ?>>
          <span>
            <strong><?php echo e($a['full_name']); ?></strong>
            <span class="muted small"> — <?php echo e($a['email']); ?> (<?php echo e($a['role']); ?>)</span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>
    <div class="flex mt-2">
      <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Save Assignments</button>
      <a href="/admin/projects/view.php?id=<?php echo (int)$id; ?>" class="btn btn--ghost">Cancel</a>
    </div>
  </div>
</form>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
