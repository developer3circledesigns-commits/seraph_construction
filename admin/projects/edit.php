<?php
/**
 * Admin — edit project.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$id = (int)($_GET['id'] ?? 0);
$project = Project::find($id);
if (!$project) redirect('/admin/projects', 'Project not found.', 'error');

$isSuper = Auth::isSuper($user);

// Permission: super admin, or an admin assigned to this project
if (!$isSuper) {
    $assigned = array_column(Project::assignedAdmins($id), 'id');
    if (!in_array((int)$user['id'], $assigned)) {
        http_response_code(403);
        exit('Forbidden — you are not assigned to this project.');
    }
}

$errors = [];
$old = $project;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (empty($body['name'])) $errors[] = 'Project name is required.';
    if (empty($body['client_id'])) $errors[] = 'Please select a client.';

    if (!$errors) {
        $body['progress_percentage'] = (int)$project['progress_percentage'];
        Project::update($id, $body);
        // Only super admins may change who manages the project (prevents
        // non-super admins from granting themselves access to any project).
        if ($isSuper && !empty($body['admin_ids'])) {
            Project::assignAdmins($id, (array)$body['admin_ids']);
        }
        Audit::admin((int)$user['id'], 'project_update', 'project', $id);
        SSE::broadcast(SSE::projectChannel($id), 'project', ['action' => 'updated', 'project_id' => $id]);
        redirect('/admin/projects/view?id=' . $id, 'Project updated successfully.');
    }
}

$clients = Database::all("SELECT id, company_name, contact_person FROM clients WHERE is_active = 1 ORDER BY company_name");
$admins  = Database::all("SELECT id, full_name, email FROM admins WHERE is_active = 1 ORDER BY full_name");
$assigned = array_column(Project::assignedAdmins($id), 'id');

$title = 'Edit Project';
$active = 'projects';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div>
    <h1>Edit: <?php echo e($project['name']); ?></h1>
    <p><a href="/admin/projects/view?id=<?php echo (int)$project['id']; ?>">&larr; Back to project</a></p>
  </div>
</div>

<form method="POST" action="/admin/projects/edit?id=<?php echo (int)$id; ?>">
  <?php echo CSRF::field(); ?>
  <div class="card">
    <h2 class="card__title mb-2">Project Details</h2>
    <div class="form-group">
      <label class="form-label" for="name">Project Name *</label>
      <input class="form-control" type="text" id="name" name="name" required value="<?php echo e($old['name']); ?>">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="client_id">Client *</label>
        <select class="form-control" id="client_id" name="client_id" required>
          <?php foreach ($clients as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo (string)$old['client_id'] === (string)$c['id'] ? 'selected' : ''; ?>>
              <?php echo e($c['company_name'] ?: $c['contact_person']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="location">Location</label>
        <input class="form-control" type="text" id="location" name="location" value="<?php echo e($old['location']); ?>">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Description</label>
      <textarea class="form-control" id="description" name="description"><?php echo e($old['description']); ?></textarea>
    </div>

    <div class="form-row form-row--3">
      <div class="form-group">
        <label class="form-label" for="start_date">Start Date</label>
        <input class="form-control" type="date" id="start_date" name="start_date" value="<?php echo e($old['start_date']); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="estimated_end_date">Estimated End</label>
        <input class="form-control" type="date" id="estimated_end_date" name="estimated_end_date" value="<?php echo e($old['estimated_end_date']); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="actual_end_date">Actual End</label>
        <input class="form-control" type="date" id="actual_end_date" name="actual_end_date" value="<?php echo e($old['actual_end_date']); ?>">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="status">Status</label>
      <select class="form-control" id="status" name="status">
        <?php foreach (['planning','in_progress','on_hold','completed','cancelled'] as $s): ?>
          <option value="<?php echo $s; ?>" <?php echo $old['status'] === $s ? 'selected' : ''; ?>><?php echo e(ucfirst(str_replace('_', ' ', $s))); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label" for="budget">Budget (₹)</label>
      <input class="form-control" type="number" step="0.01" min="0" id="budget" name="budget" value="<?php echo e($old['budget']); ?>">
    </div>
  </div>

  <?php if ($isSuper): ?>
  <div class="card">
    <h2 class="card__title mb-2">Assign Admins</h2>
    <div class="checklist">
      <?php foreach ($admins as $a): ?>
        <label>
          <input type="checkbox" name="admin_ids[]" value="<?php echo (int)$a['id']; ?>"
            <?php echo in_array($a['id'], $assigned) ? 'checked' : ''; ?>>
          <span>
            <strong><?php echo e($a['full_name']); ?></strong>
            <span class="muted small"> — <?php echo e($a['email']); ?></span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Save Changes</button>
    <a href="/admin/projects/view?id=<?php echo (int)$id; ?>" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
