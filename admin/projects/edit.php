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

if (!$isSuper) {
    $assigned = array_column(Project::assignedAdmins($id), 'id');
    if (!in_array((int)$user['id'], $assigned)) {
        http_response_code(403);
        exit('Forbidden — you are not assigned to this project.');
    }
}

$errors = [];
$old = $project;
$layout = Project::getLayout($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (empty($body['name'])) $errors[] = 'Project name is required.';
    if (empty($body['client_id'])) $errors[] = 'Please select a client.';
    $prog = (int)($body['progress_percentage'] ?? 0);
    if ($prog < 0 || $prog > 100) $errors[] = 'Progress must be between 0 and 100.';

    if (!$errors) {
        $category = ($body['category'] ?? '') === 'custom'
            ? trim($body['custom_category'] ?? '')
            : ($body['category'] ?? '');

        $thumbnailPath = $project['thumbnail'] ?? null;

        if (isset($body['delete_thumbnail']) && $body['delete_thumbnail']) {
            if ($thumbnailPath && file_exists(dirname(__DIR__, 2) . '/' . $thumbnailPath)) {
                unlink(dirname(__DIR__, 2) . '/' . $thumbnailPath);
            }
            $thumbnailPath = null;
        }

        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $uploadDir = dirname(__DIR__, 2) . '/images/projects/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $thumbName = basename($_FILES['thumbnail']['name']);
            $thumbExt  = pathinfo($thumbName, PATHINFO_EXTENSION);
            $thumbBase = preg_replace('/[^a-z0-9_-]/', '', strtolower(pathinfo($thumbName, PATHINFO_FILENAME)));
            $thumbBase = substr($thumbBase ?: 'project', 0, 40);
            $targetName = $thumbBase . '_' . time() . '.' . $thumbExt;
            $targetPath = $uploadDir . $targetName;

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($_FILES['thumbnail']['tmp_name']);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
                $errors[] = 'Thumbnail must be a JPG, PNG, WEBP, or GIF image.';
            } elseif ($_FILES['thumbnail']['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Thumbnail must be under 5MB.';
            } elseif (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
                $errors[] = 'Failed to upload thumbnail.';
            } else {
                if ($thumbnailPath && file_exists(dirname(__DIR__, 2) . '/' . $thumbnailPath)) {
                    unlink(dirname(__DIR__, 2) . '/' . $thumbnailPath);
                }
                $thumbnailPath = 'images/projects/' . $targetName;
            }
        }

        if (!$errors) {
            $body['category']  = $category;
            $body['thumbnail'] = $thumbnailPath;
            Project::update($id, $body);

            if ($isSuper && !empty($body['admin_ids'])) {
                Project::assignAdmins($id, (array)$body['admin_ids']);
            }

            if (isset($body['delete_layout']) && $body['delete_layout']) {
                Project::deleteLayout($id);
                $layout = null;
            }

            if (!empty($_FILES['layout_file']['tmp_name'])) {
                try {
                    Project::uploadLayout($id, $_FILES['layout_file']);
                    $layout = Project::getLayout($id);
                } catch (RuntimeException $e) {
                    $errors[] = 'Layout upload: ' . $e->getMessage();
                }
            }

            if (!$errors) {
                Audit::admin((int)$user['id'], 'project_update', 'project', $id);
                SSE::broadcast(SSE::projectChannel($id), 'project', ['action' => 'updated', 'project_id' => $id]);
                redirect('/admin/projects/view?id=' . $id, 'Project updated successfully.');
            }
        }
    }
}

$clients  = Database::all("SELECT id, company_name, contact_person FROM clients WHERE is_active = 1 ORDER BY company_name");
$admins   = Database::all("SELECT id, full_name, email FROM admins WHERE is_active = 1 ORDER BY full_name");
$assigned = array_column(Project::assignedAdmins($id), 'id');

$oldCategory = $project['category'] ?? '';
$isCustomCategory = !in_array($oldCategory, array_merge(Project::CATEGORIES, ['']));

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

<form method="POST" action="/admin/projects/edit?id=<?php echo (int)$id; ?>" enctype="multipart/form-data">
  <?php echo CSRF::field(); ?>
  <div class="card">
    <h2 class="card__title mb-2">Project Details</h2>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="name">Project Name *</label>
        <input class="form-control" type="text" id="name" name="name" required value="<?php echo e($old['name']); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="category">Project Category</label>
        <select class="form-control" id="category" name="category" onchange="document.getElementById('customCatRow').style.display = this.value === 'custom' ? '' : 'none';">
          <option value="">Select category...</option>
          <?php foreach (Project::CATEGORIES as $cat): ?>
            <option value="<?php echo e($cat); ?>" <?php echo (!$isCustomCategory && $oldCategory === $cat) ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
          <?php endforeach; ?>
          <option value="custom" <?php echo $isCustomCategory ? 'selected' : ''; ?>>Other (custom)</option>
        </select>
      </div>
    </div>

    <div class="form-group" id="customCatRow" style="<?php echo $isCustomCategory ? '' : 'display:none;'; ?>">
      <label class="form-label" for="custom_category">Custom Category</label>
      <input class="form-control" type="text" id="custom_category" name="custom_category" value="<?php echo e($isCustomCategory ? $oldCategory : ''); ?>" placeholder="Enter custom category">
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Description</label>
      <textarea class="form-control" id="description" name="description"><?php echo e($old['description']); ?></textarea>
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

    <div class="form-row form-row--3">
      <div class="form-group">
        <label class="form-label" for="plot_size">Plot Size</label>
        <input class="form-control" type="text" id="plot_size" name="plot_size" value="<?php echo e($old['plot_size']); ?>" placeholder="e.g. 30x40 or 2400 sqft">
      </div>
      <div class="form-group">
        <label class="form-label" for="built_up_area">Built-up Area</label>
        <input class="form-control" type="text" id="built_up_area" name="built_up_area" value="<?php echo e($old['built_up_area']); ?>" placeholder="e.g. 2400 sqft">
      </div>
      <div class="form-group">
        <label class="form-label" for="style">Style</label>
        <input class="form-control" type="text" id="style" name="style" value="<?php echo e($old['style']); ?>" placeholder="e.g. Modern Contemporary">
      </div>
    </div>

    <div class="form-row form-row--3">
      <div class="form-group">
        <label class="form-label" for="floors">Floors</label>
        <input class="form-control" type="number" min="0" id="floors" name="floors" value="<?php echo e($old['floors'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="bedrooms">Bedrooms</label>
        <input class="form-control" type="number" min="0" id="bedrooms" name="bedrooms" value="<?php echo e($old['bedrooms'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="bathrooms">Bathrooms</label>
        <input class="form-control" type="number" min="0" id="bathrooms" name="bathrooms" value="<?php echo e($old['bathrooms'] ?? ''); ?>">
      </div>
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

    <div class="form-group">
      <label class="form-label" for="budget">Budget (₹)</label>
      <input class="form-control" type="number" step="0.01" min="0" id="budget" name="budget" value="<?php echo e($old['budget']); ?>">
    </div>
  </div>

  <div class="card">
    <h2 class="card__title mb-2">Media &amp; Files</h2>

    <div class="form-group">
      <label class="form-label" for="thumbnail">Project Thumbnail</label>
      <?php if (!empty($project['thumbnail']) && file_exists(dirname(__DIR__, 2) . '/' . $project['thumbnail'])): ?>
        <div class="mb-1">
          <img src="/<?php echo e($project['thumbnail']); ?>" alt="Current thumbnail" style="max-width:180px;max-height:120px;border-radius:4px;">
          <label class="small" style="display:inline-flex;align-items:center;gap:4px;margin-left:0.8rem;">
            <input type="checkbox" name="delete_thumbnail" value="1"> Remove current
          </label>
        </div>
      <?php endif; ?>
      <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp,image/gif">
      <span class="small muted">JPG, PNG, WEBP or GIF. Max 5MB. Leave empty to keep current.</span>
    </div>

    <div class="form-group">
      <label class="form-label" for="layout_file">Download Layout File</label>
      <?php if ($layout): ?>
        <div class="mb-1">
          <span class="badge"><i class="fa-solid fa-file"></i> <?php echo e($layout['original_name']); ?></span>
          <span class="small muted">(<?php echo number_format((int)$layout['file_size'] / 1024, 1); ?> KB)</span>
          <label class="small" style="display:inline-flex;align-items:center;gap:4px;margin-left:0.8rem;">
            <input type="checkbox" name="delete_layout" value="1"> Remove current
          </label>
        </div>
      <?php endif; ?>
      <input class="form-control" type="file" id="layout_file" name="layout_file" accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.dwg,.dxf,.svg,.zip">
      <span class="small muted">PDF, image, CAD, or any format. Max 10MB. Uploading replaces the current file.</span>
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
