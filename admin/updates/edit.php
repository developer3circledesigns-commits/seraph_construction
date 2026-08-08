<?php
/**
 * Admin — edit a daily update.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$id = (int)($_GET['id'] ?? 0);
$update = DailyUpdate::find($id);
if (!$update) redirect('/admin/projects', 'Update not found.', 'error');

$project = Project::find((int)$update['project_id']);

// Permission: super admin, or the original author, or assigned admin
if (!Auth::isSuper($user)) {
    $assigned = array_column(Project::assignedAdmins((int)$project['id']), 'id');
    $allowed = (int)$update['admin_id'] === (int)$user['id'] || in_array((int)$user['id'], $assigned);
    if (!$allowed) {
        http_response_code(403);
        exit('Forbidden — you cannot edit this update.');
    }
}

$errors = [];
$old = [
    'update_date'         => $update['update_date'],
    'status'              => $update['status'],
    'progress_percentage' => (int)$update['progress_percentage'],
    'title'               => $update['title'],
    'description'         => $update['description'],
    'materials_used'      => $update['materials_used'],
    'labor_count'         => $update['labor_count'],
    'next_day_plan'       => $update['next_day_plan'],
    'is_milestone'        => $update['is_milestone'],
];

$existingImages = json_decode_col($update['images'] ?? null) ?: [];
$existingImageIds = array_values(array_filter(array_map('intval', (array)$existingImages), fn($v) => $v > 0));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    $body['progress_percentage'] = $old['progress_percentage'];
    $errors = DailyUpdate::validate($body);

    // Date conflict check (excluding self)
    if ($body['update_date'] !== $update['update_date']) {
        if (DailyUpdate::existsForDate((int)$project['id'], $body['update_date'] ?? '')) {
            $errors[] = 'An update already exists for this date.';
        }
    }

    // Remove images flagged for deletion (the JS keeps them in a
    // comma-separated hidden field).
    $remove = [];
    foreach ((array)($body['remove_images'] ?? []) as $chunk) {
        foreach (explode(',', (string)$chunk) as $v) {
            $v = (int)trim($v);
            if ($v > 0) {
                $remove[] = $v;
            }
        }
    }

    if (!$errors) {
        $imageIds = $existingImageIds;

        // Process new uploads / deletions ONLY after validation passes so a
        // failed edit cannot orphan newly-written image blobs.
        if (!empty($_FILES['images'])) {
            $newIds = Image::storeMany($_FILES['images'], (int)$update['id']);
            $imageIds = array_merge($imageIds, $newIds);
        }

        if (!empty($remove)) {
            $imageIds = array_values(array_diff($imageIds, $remove));
            Image::deleteMany($remove);
        }

        $body['images'] = $imageIds;
        DailyUpdate::update((int)$update['id'], $body);

        // Sync project status/progress (preserve existing completion date;
        // set it on completion if it was never recorded).
        Project::update((int)$project['id'], [
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

        Audit::admin((int)$user['id'], 'update_edit', 'daily_update', $id);

        SSE::broadcast(SSE::projectChannel((int)$project['id']), 'status_update', [
            'project_id' => (int)$project['id'],
            'update_id'  => $id,
            'status'     => $body['status'],
            'progress'   => (int)$body['progress_percentage'],
            'title'      => $body['title'],
            'date'       => $body['update_date'],
        ]);

        redirect('/admin/projects/view?id=' . (int)$project['id'], 'Update saved successfully.');
    }
}

$title = 'Edit Update';
$active = 'projects';
include __DIR__ . '/../partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div>
    <h1>Edit Update</h1>
    <p><a href="/admin/projects/view?id=<?php echo (int)$project['id']; ?>">&larr; <?php echo e($project['name']); ?></a></p>
  </div>
</div>

<form method="POST" action="/admin/updates/edit?id=<?php echo (int)$id; ?>" enctype="multipart/form-data">
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
      <input class="form-control" type="text" id="title" name="title" required value="<?php echo e($old['title']); ?>">
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Description</label>
      <textarea class="form-control" id="description" name="description"><?php echo e($old['description']); ?></textarea>
    </div>

    <div class="form-group">
      <label class="form-label" for="labor_count">Workers on site</label>
      <input class="form-control" type="number" min="0" id="labor_count" name="labor_count" value="<?php echo e($old['labor_count']); ?>">
    </div>

    <div class="form-group">
      <label class="form-label" for="materials_used">Materials Used</label>
      <input class="form-control" type="text" id="materials_used" name="materials_used" value="<?php echo e($old['materials_used']); ?>">
    </div>

    <div class="form-group">
      <label class="form-label" for="next_day_plan">Next Day Plan</label>
      <textarea class="form-control" id="next_day_plan" name="next_day_plan"><?php echo e($old['next_day_plan']); ?></textarea>
    </div>

    <?php if (!empty($existingImages)): ?>
      <div class="form-group">
        <label class="form-label">Current Photos</label>
        <div class="gallery">
          <?php foreach ($existingImages as $img): ?>
            <div class="gallery__item" style="cursor:default">
              <img src="/image?id=<?php echo $img; ?>" alt="Existing">
              <div class="image-id" style="display:none;"><?php echo $img; ?></div>
              <button type="button" class="remove-img-btn" data-id="<?php echo $img; ?>"
                style="position:absolute;top:6px;right:6px;background:rgba(224,91,91,0.9);color:#fff;border:none;border-radius:50%;width:26px;height:26px;cursor:pointer" title="Remove photo">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="remove_images[]" id="removeImages" value="">
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label class="form-label">Add More Photos</label>
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
        <span><strong>Mark as Milestone</strong></span>
      </label>
    </div>
  </div>

  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Save Changes</button>
    <a href="/admin/projects/view?id=<?php echo (int)$project['id']; ?>" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>
