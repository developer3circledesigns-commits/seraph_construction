<?php
/**
 * Admin — project detail + daily update timeline.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$id = (int)($_GET['id'] ?? 0);
$project = Project::find($id);
if (!$project) redirect('/admin/projects', 'Project not found.', 'error');

$updates = DailyUpdate::forProject($id);
$assignedAdmins = Project::assignedAdmins($id);
$layout = Project::getLayout($id);

if (!Auth::isSuper($user)) {
    $isAssigned = false;
    foreach ($assignedAdmins as $a) {
        if ((int)$a['id'] === (int)$user['id']) { $isAssigned = true; break; }
    }
    if (!$isAssigned) {
        http_response_code(403);
        exit('Forbidden — you are not assigned to this project.');
    }
}

$title = $project['name'];
$active = 'projects';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1><?php echo e($project['name']); ?></h1>
    <p><?php echo e($project['company_name'] ?: $project['contact_person']); ?>
      <?php if ($project['location']): ?> &middot; <?php echo e($project['location']); ?><?php endif; ?></p>
  </div>
  <div class="flex flex--wrap">
    <span class="live-dot" id="liveIndicator">Live</span>
    <a href="/admin/updates/create?project_id=<?php echo (int)$id; ?>" class="btn btn--primary">
      <i class="fa-solid fa-calendar-plus"></i> Add Daily Update
    </a>
    <a href="/admin/projects/edit?id=<?php echo (int)$id; ?>" class="btn btn--secondary"><i class="fa-solid fa-pen"></i> Edit</a>
    <a href="/admin/projects/assign?id=<?php echo (int)$id; ?>" class="btn btn--secondary"><i class="fa-solid fa-user-gear"></i> Assign</a>
  </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr))">
  <div class="stat-card">
    <div class="stat-card__label">Status</div>
    <div class="stat-card__value"><span class="badge badge--<?php echo e($project['status']); ?>"><?php echo e(str_replace('_', ' ', $project['status'])); ?></span></div>
  </div>
  <div class="stat-card stat-card--gold">
    <div class="stat-card__label">Progress</div>
    <div class="stat-card__value"><?php echo (int)$project['progress_percentage']; ?>%</div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">Updates</div>
    <div class="stat-card__value"><?php echo count($updates); ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">Budget</div>
    <div class="stat-card__value small" style="font-size:1.05rem"><?php echo $project['budget'] ? money($project['budget']) : '—'; ?></div>
  </div>
</div>

<div class="grid-2">
  <div>
    <div class="card">
      <div class="card__header">
        <h2 class="card__title">Timeline</h2>
        <a href="/admin/updates/create?project_id=<?php echo (int)$id; ?>" class="btn btn--primary btn--sm"><i class="fa-solid fa-plus"></i> Update</a>
      </div>

      <?php if (empty($updates)): ?>
        <div class="empty-state">
          <i class="fa-solid fa-calendar-xmark"></i>
          No updates yet. Post your first daily update.
        </div>
      <?php endif; ?>

      <div class="timeline">
        <?php foreach ($updates as $u): ?>
          <?php
            $images = json_decode_col($u['images'] ?? null) ?: [];
          ?>
          <div class="timeline__item <?php echo $u['is_milestone'] ? 'timeline__item--milestone' : ''; ?>" data-update-id="<?php echo (int)$u['id']; ?>">
            <span class="timeline__dot"></span>
            <div class="timeline__meta">
              <span class="timeline__date"><i class="fa-solid fa-calendar-day"></i> <?php echo e(date('d M Y', strtotime($u['update_date']))); ?></span>
              <span class="badge badge--<?php echo e($u['status']); ?>"><?php echo e(str_replace('_', ' ', $u['status'])); ?></span>
              <?php if ($u['is_milestone']): ?><span class="badge badge--milestone"><i class="fa-solid fa-star"></i> Milestone</span><?php endif; ?>
              <span class="small muted">by <?php echo e($u['admin_name']); ?> &middot; <?php echo e(time_ago($u['created_at'])); ?></span>
            </div>
            <div class="timeline__title"><?php echo e($u['title']); ?></div>
            <?php if ($u['description']): ?><div class="timeline__body" style="white-space:pre-line"><?php echo e($u['description']); ?></div><?php endif; ?>

            <?php if (!empty($images)): ?>
              <div class="gallery">
                <?php foreach ($images as $img): ?>
                  <div class="gallery__item">
                    <img src="/image?id=<?php echo $img; ?>" alt="Update photo" loading="lazy">
                    <div class="image-id" style="display:none;"><?php echo $img; ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="timeline__details">
              <?php if ($u['materials_used']): ?><span class="detail-chip"><i class="fa-solid fa-layer-group"></i> <?php echo e($u['materials_used']); ?></span><?php endif; ?>
              <?php if ($u['labor_count'] !== null): ?><span class="detail-chip"><i class="fa-solid fa-user-group"></i> <?php echo (int)$u['labor_count']; ?> workers</span><?php endif; ?>
              <?php if ($u['weather_condition']): ?><span class="detail-chip"><i class="fa-solid fa-cloud-sun"></i> <?php echo e($u['weather_condition']); ?></span><?php endif; ?>
            </div>

            <?php if ($u['next_day_plan']): ?>
              <div class="mt-1 small muted"><strong class="muted">Next day plan:</strong> <?php echo e($u['next_day_plan']); ?></div>
            <?php endif; ?>

            <div class="flex mt-1">
              <a class="btn btn--ghost btn--sm" href="/admin/updates/edit?id=<?php echo (int)$u['id']; ?>" aria-label="Edit update <?php echo e($u['title']); ?>"><i class="fa-solid fa-pen"></i> Edit</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <h2 class="card__title mb-2">Project Info</h2>
      <div class="small">
        <div class="flex flex--between mb-1"><span class="muted">Client</span><strong><?php echo e($project['company_name'] ?: '—'); ?></strong></div>
        <div class="flex flex--between mb-1"><span class="muted">Contact</span><strong><?php echo e($project['contact_person'] ?: '—'); ?></strong></div>
        <div class="flex flex--between mb-1"><span class="muted">Email</span><strong><?php echo e($project['client_email'] ?: '—'); ?></strong></div>
        <?php if ($project['category']): ?><div class="flex flex--between mb-1"><span class="muted">Category</span><strong><?php echo e($project['category']); ?></strong></div><?php endif; ?>
        <?php if ($project['location']): ?><div class="flex flex--between mb-1"><span class="muted">Location</span><strong><?php echo e($project['location']); ?></strong></div><?php endif; ?>
        <?php if ($project['plot_size']): ?><div class="flex flex--between mb-1"><span class="muted">Plot Size</span><strong><?php echo e($project['plot_size']); ?></strong></div><?php endif; ?>
        <?php if ($project['built_up_area']): ?><div class="flex flex--between mb-1"><span class="muted">Built-up Area</span><strong><?php echo e($project['built_up_area']); ?></strong></div><?php endif; ?>
        <?php if ($project['floors'] !== null): ?><div class="flex flex--between mb-1"><span class="muted">Floors</span><strong><?php echo (int)$project['floors']; ?></strong></div><?php endif; ?>
        <?php if ($project['bedrooms'] !== null): ?><div class="flex flex--between mb-1"><span class="muted">Bedrooms</span><strong><?php echo (int)$project['bedrooms']; ?></strong></div><?php endif; ?>
        <?php if ($project['bathrooms'] !== null): ?><div class="flex flex--between mb-1"><span class="muted">Bathrooms</span><strong><?php echo (int)$project['bathrooms']; ?></strong></div><?php endif; ?>
        <?php if ($project['style']): ?><div class="flex flex--between mb-1"><span class="muted">Style</span><strong><?php echo e($project['style']); ?></strong></div><?php endif; ?>
        <hr style="border-color:var(--color-surface-2);margin:0.6rem 0;">
        <div class="flex flex--between mb-1"><span class="muted">Start</span><strong><?php echo e($project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '—'); ?></strong></div>
        <div class="flex flex--between mb-1"><span class="muted">Est. End</span><strong><?php echo e($project['estimated_end_date'] ? date('d M Y', strtotime($project['estimated_end_date'])) : '—'); ?></strong></div>
        <div class="flex flex--between mb-1"><span class="muted">Created</span><strong><?php echo e(date('d M Y', strtotime($project['created_at']))); ?></strong></div>
      </div>
    </div>

    <div class="card">
      <h2 class="card__title mb-2">Layout File</h2>
      <?php if ($layout): ?>
        <div class="small">
          <div class="flex flex--between mb-1"><span class="muted">File</span><strong><?php echo e($layout['original_name']); ?></strong></div>
          <div class="flex flex--between mb-1"><span class="muted">Type</span><strong><?php echo e($layout['file_type']); ?></strong></div>
          <div class="flex flex--between mb-1"><span class="muted">Size</span><strong><?php echo number_format((int)$layout['file_size'] / 1024, 1); ?> KB</strong></div>
          <a href="/api/download-layout.php?id=<?php echo (int)$id; ?>" class="btn btn--secondary btn--sm mt-1"><i class="fa-solid fa-download"></i> Download</a>
        </div>
      <?php else: ?>
        <p class="muted small">No layout file uploaded.</p>
      <?php endif; ?>
    </div>

    <div class="card">
      <h2 class="card__title mb-2">Assigned Admins</h2>
      <?php if (empty($assignedAdmins)): ?>
        <p class="muted small">No admins assigned.</p>
      <?php endif; ?>
      <?php foreach ($assignedAdmins as $a): ?>
        <div class="flex flex--between mb-1">
          <span><i class="fa-solid fa-user-shield muted"></i> <?php echo e($a['full_name']); ?></span>
          <span class="badge <?php echo $a['role'] === 'super_admin' ? 'badge--milestone' : ''; ?>" style="background:var(--color-surface-2);color:var(--color-text-muted)"><?php echo e($a['role']); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
