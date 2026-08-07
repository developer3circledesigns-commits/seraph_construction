<?php
/**
 * Admin — create a daily update for a project.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$projectId = (int)($_GET['project_id'] ?? 0);
$project = Project::find($projectId);
if (!$project) redirect('/admin/projects', 'Project not found.', 'error');

// Only super admins or assigned admins can post
if (!Auth::isSuper($user)) {
    $assigned = array_column(Project::assignedAdmins($projectId), 'id');
    if (!in_array((int)$user['id'], $assigned)) {
        http_response_code(403);
        exit('Forbidden — you are not assigned to this project.');
    }
}

$errors = [];
$old = [
    'update_date'         => date('Y-m-d'),
    'status'              => $project['status'],
    'progress_percentage' => (int)$project['progress_percentage'],
    'title'               => '',
    'description'         => '',
    'materials_used'      => '',
    'labor_count'         => '',
    'weather_condition'   => '',
    'next_day_plan'       => '',
    'is_milestone'        => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    $errors = DailyUpdate::validate($body);

    if (DailyUpdate::existsForDate($projectId, $body['update_date'] ?? '')) {
        $errors[] = 'An update already exists for this date. Choose another date or edit the existing one.';
    }

    if (!$errors) {
        $body['project_id'] = $projectId;
        $body['admin_id'] = (int)$user['id'];
        $body['images'] = [];

        // The uq_project_date unique key is the real guard against a
        // concurrent duplicate; catch it here so it fails gracefully
        // instead of surfacing as a 500.
        try {
            $updateId = DailyUpdate::create($body);
        } catch (\PDOException $e) {
            $sqlState = (string)($e->errorInfo[0] ?? '');
            if ($sqlState === '23000' && DailyUpdate::existsForDate($projectId, $body['update_date'] ?? '')) {
                $errors[] = 'An update already exists for this date. Choose another date or edit the existing one.';
            } else {
                throw $e;
            }
        }

        if (!isset($updateId) || $updateId <= 0) {
            $updateId = null;
        }

        if ($updateId) {
        // Store photos against the freshly-created update row (DB blobs)
        $imageIds = [];
        if (!empty($_FILES['images'])) {
            $imageIds = Image::storeMany($_FILES['images'], $updateId);
        }
        if (!empty($imageIds)) {
            DailyUpdate::setImages($updateId, $imageIds);
        }

        // Sync project status/progress (preserve existing completion date;
        // set it on completion if it was never recorded).
        Project::update($projectId, [
            'client_id'           => $project['client_id'],
            'name'                => $project['name'],
            'description'         => $project['description'],
            'location'            => $project['location'],
            'start_date'          => $project['start_date'],
            'estimated_end_date'  => $project['estimated_end_date'],
            'actual_end_date'     => $body['status'] === 'completed'
                ? ($project['actual_end_date'] ?: $body['update_date'])
                : $project['actual_end_date'],
            'status'              => $body['status'],
            'progress_percentage' => (int)$body['progress_percentage'],
            'budget'              => $project['budget'],
        ]);

        Audit::admin((int)$user['id'], 'update_create', 'daily_update', $updateId, ['project_id' => $projectId]);

        // Real-time broadcast to project channel + notifications
        $eventType = !empty($body['is_milestone']) ? 'milestone' : 'status_update';
        SSE::broadcast(SSE::projectChannel($projectId), $eventType, [
            'project_id' => $projectId,
            'update_id'  => $updateId,
            'status'     => $body['status'],
            'progress'   => (int)$body['progress_percentage'],
            'title'      => $body['title'],
            'date'       => $body['update_date'],
        ]);

        Notification::notifyProjectParties(
            $projectId,
            (int)$project['client_id'],
            $eventType,
            ($body['is_milestone'] ? 'Milestone: ' : '') . $body['title'],
            'Progress is now at ' . (int)$body['progress_percentage'] . '% for ' . $project['name'] . '.',
            $updateId
        );

        redirect('/admin/projects/view?id=' . $projectId, 'Daily update posted successfully.');
        }
    }
}

$title = 'Add Daily Update';
$active = 'projects';
include __DIR__ . '/../partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div>
    <h1>Daily Update</h1>
    <p><a href="/admin/projects/view?id=<?php echo (int)$projectId; ?>">&larr; <?php echo e($project['name']); ?></a></p>
  </div>
</div>

<form method="POST" action="/admin/updates/create?project_id=<?php echo (int)$projectId; ?>" enctype="multipart/form-data">
  <?php echo CSRF::field(); ?>
  <div class="card">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="update_date">Date *</label>
        <input class="form-control" type="date" id="update_date" name="update_date" required value="<?php echo e($old['update_date']); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="status">Status</label>
        <select class="form-control" id="status" name="status">
          <?php foreach (['planning','in_progress','on_hold','completed','cancelled'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $old['status'] === $s ? 'selected' : ''; ?>><?php echo e(ucfirst(str_replace('_', ' ', $s))); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="title">Update Title *</label>
      <input class="form-control" type="text" id="title" name="title" required value="<?php echo e($old['title']); ?>" placeholder="e.g. Ground floor slab concreting completed">
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Description</label>
      <textarea class="form-control" id="description" name="description" placeholder="What was done today?"><?php echo e($old['description']); ?></textarea>
    </div>

    <div class="form-row form-row--3">
      <div class="form-group">
        <label class="form-label" for="progress_percentage">Progress (%)</label>
        <input class="form-control" type="number" min="0" max="100" id="progress_percentage" name="progress_percentage" value="<?php echo (int)$old['progress_percentage']; ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="labor_count">Workers on site</label>
        <input class="form-control" type="number" min="0" id="labor_count" name="labor_count" value="<?php echo e($old['labor_count']); ?>" placeholder="e.g. 24">
      </div>
      <div class="form-group">
        <label class="form-label" for="weather_condition">Weather</label>
        <input class="form-control" type="text" id="weather_condition" name="weather_condition" value="<?php echo e($old['weather_condition']); ?>" placeholder="e.g. Sunny, 34°C">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="materials_used">Materials Used</label>
      <input class="form-control" type="text" id="materials_used" name="materials_used" value="<?php echo e($old['materials_used']); ?>" placeholder="e.g. Cement, Steel, M20 concrete">
    </div>

    <div class="form-group">
      <label class="form-label" for="next_day_plan">Next Day Plan</label>
      <textarea class="form-control" id="next_day_plan" name="next_day_plan" placeholder="What's planned for tomorrow?"><?php echo e($old['next_day_plan']); ?></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Photos (optional, max 5MB each)</label>
      <div class="dropzone" id="dropzone">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <div>Drag & drop images here, or click to browse</div>
        <input type="file" name="images[]" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
      </div>
      <div class="flex flex--wrap mt-1" id="preview"></div>
    </div>

    <div class="form-group">
      <label class="flex" style="cursor:pointer;gap:10px">
        <input type="checkbox" name="is_milestone" value="1" style="accent-color:var(--color-gold);width:16px;height:16px"
          <?php echo !empty($old['is_milestone']) ? 'checked' : ''; ?>>
        <span><strong>Mark as Milestone</strong> <span class="muted small">(e.g. foundation done, slab pour, roof complete)</span></span>
      </label>
    </div>
  </div>

  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-paper-plane"></i> Post Update</button>
    <a href="/admin/projects/view?id=<?php echo (int)$projectId; ?>" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>
