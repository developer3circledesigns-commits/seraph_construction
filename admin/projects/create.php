<?php
/**
 * Admin — create project.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$errors = [];
$old = [
    'client_id'           => '',
    'name'                => '',
    'description'         => '',
    'location'            => '',
    'start_date'          => '',
    'estimated_end_date'  => '',
    'status'              => 'planning',
    'progress_percentage' => 0,
    'budget'              => '',
];

$clients = Database::all("SELECT id, company_name, contact_person FROM clients WHERE is_active = 1 ORDER BY company_name");
$admins  = Database::all("SELECT id, full_name, email FROM admins WHERE is_active = 1 ORDER BY full_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (empty($body['name'])) $errors[] = 'Project name is required.';
    if (empty($body['client_id'])) $errors[] = 'Please select a client.';
    $prog = (int)($body['progress_percentage'] ?? 0);
    if ($prog < 0 || $prog > 100) $errors[] = 'Progress must be between 0 and 100.';

    if (!$errors) {
        $id = Project::create($body);
        if (!empty($body['admin_ids'])) {
            Project::assignAdmins($id, (array)$body['admin_ids']);
        }
        Audit::admin((int)$user['id'], 'project_create', 'project', $id, ['name' => $body['name']]);
        SSE::broadcast(SSE::projectChannel($id), 'project', ['action' => 'created', 'project_id' => $id]);
        redirect('/admin/projects/view?id=' . $id, 'Project created successfully.');
    }
}

$title = 'New Project';
$active = 'project_new';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div>
    <h1>New Project</h1>
    <p>Create a project and assign it to a client.</p>
  </div>
</div>

<form method="POST" action="/admin/projects/create">
  <?php echo CSRF::field(); ?>
  <div class="card">
    <h2 class="card__title mb-2">Project Details</h2>
    <div class="form-group">
      <label class="form-label" for="name">Project Name *</label>
      <input class="form-control" type="text" id="name" name="name" required value="<?php echo e($old['name']); ?>" placeholder="e.g. Villa Azure">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="client_id">Client *</label>
        <select class="form-control" id="client_id" name="client_id" required>
          <option value="">Select client...</option>
          <?php foreach ($clients as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo (string)$old['client_id'] === (string)$c['id'] ? 'selected' : ''; ?>>
              <?php echo e($c['company_name'] ?: $c['contact_person']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="location">Location</label>
        <input class="form-control" type="text" id="location" name="location" value="<?php echo e($old['location']); ?>" placeholder="e.g. Anna Nagar, Chennai">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Description</label>
      <textarea class="form-control" id="description" name="description" placeholder="Brief description of the project..."><?php echo e($old['description']); ?></textarea>
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
        <label class="form-label" for="budget">Budget (₹)</label>
        <input class="form-control" type="number" step="0.01" min="0" id="budget" name="budget" value="<?php echo e($old['budget']); ?>" placeholder="e.g. 5000000">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="status">Status</label>
        <select class="form-control" id="status" name="status">
          <?php foreach (['planning','in_progress','on_hold','completed','cancelled'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $old['status'] === $s ? 'selected' : ''; ?>><?php echo e(ucfirst(str_replace('_', ' ', $s))); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="progress_percentage">Progress (%)</label>
        <input class="form-control" type="number" min="0" max="100" id="progress_percentage" name="progress_percentage" value="<?php echo (int)$old['progress_percentage']; ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <h2 class="card__title mb-2">Assign Admins</h2>
    <p class="small muted mb-2">Choose which admin(s) manage this project.</p>
    <div class="checklist">
      <?php foreach ($admins as $a): ?>
        <label>
          <input type="checkbox" name="admin_ids[]" value="<?php echo (int)$a['id']; ?>"
            <?php echo in_array($a['id'], (array)($old['admin_ids'] ?? [])) ? 'checked' : ''; ?>>
          <span>
            <strong><?php echo e($a['full_name']); ?></strong>
            <span class="muted small"> — <?php echo e($a['email']); ?></span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Create Project</button>
    <a href="/admin/projects" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
