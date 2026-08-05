<?php
/**
 * Client portal — project detail + timeline (read-only).
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::CLIENT, '/client/login.php');

$id = (int)($_GET['id'] ?? 0);
$project = Project::findForClient($id, (int)$user['id']);
if (!$project) redirect('/client/', 'Project not found.', 'error');

$updates = DailyUpdate::forProject($id);

$title = $project['name'];
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="project-head">
  <div>
    <h1><?php echo e($project['name']); ?></h1>
    <?php if ($project['location']): ?><p class="muted"><?php echo e($project['location']); ?></p><?php endif; ?>
  </div>
  <div class="flex">
    <span class="badge badge--<?php echo e($project['status']); ?>"><?php echo e(str_replace('_', ' ', $project['status'])); ?></span>
    <a href="/client/" class="btn btn--secondary btn--sm">&larr; All Projects</a>
  </div>
</div>

<div class="project-summary">
  <div class="summary-card">
    <div class="summary-card__label">Overall Progress</div>
    <div class="summary-card__value">
      <div class="flex">
        <div class="progress" style="flex:1;max-width:140px"><div class="progress__bar" style="width:<?php echo (int)$project['progress_percentage']; ?>%"></div></div>
        <span><?php echo (int)$project['progress_percentage']; ?>%</span>
      </div>
    </div>
  </div>
  <div class="summary-card">
    <div class="summary-card__label">Start Date</div>
    <div class="summary-card__value"><?php echo $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '—'; ?></div>
  </div>
  <div class="summary-card">
    <div class="summary-card__label">Estimated Completion</div>
    <div class="summary-card__value"><?php echo $project['estimated_end_date'] ? date('d M Y', strtotime($project['estimated_end_date'])) : '—'; ?></div>
  </div>
  <div class="summary-card">
    <div class="summary-card__label">Updates Posted</div>
    <div class="summary-card__value"><?php echo count($updates); ?></div>
  </div>
</div>

<?php if ($project['description']): ?>
  <div class="card mb-2">
    <h2 class="card__title mb-1">About this project</h2>
    <p class="muted mb-0" style="white-space:pre-line"><?php echo e($project['description']); ?></p>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card__header">
    <h2 class="card__title">Daily Updates</h2>
    <span class="small muted">Newest first</span>
  </div>

  <?php if (empty($updates)): ?>
    <div class="empty-state">
      <i class="fa-solid fa-calendar-xmark"></i>
      No updates yet. Check back soon — our team posts progress here every working day.
    </div>
  <?php endif; ?>

  <div class="timeline">
    <?php foreach ($updates as $u): ?>
      <?php $images = json_decode_col($u['images'] ?? null) ?: []; ?>
      <div class="timeline__item <?php echo $u['is_milestone'] ? 'timeline__item--milestone' : ''; ?>">
        <span class="timeline__dot"></span>
        <div class="timeline__meta">
          <span class="timeline__date"><i class="fa-solid fa-calendar-day"></i> <?php echo e(date('l, d M Y', strtotime($u['update_date']))); ?></span>
          <span class="badge badge--<?php echo e($u['status']); ?>"><?php echo e(str_replace('_', ' ', $u['status'])); ?></span>
          <?php if ($u['is_milestone']): ?><span class="badge badge--milestone"><i class="fa-solid fa-star"></i> Milestone</span><?php endif; ?>
          <span class="badge" style="background:var(--color-surface-2);color:var(--color-text-muted)"><?php echo (int)$u['progress_percentage']; ?>% complete</span>
        </div>
        <div class="timeline__title"><?php echo e($u['title']); ?></div>
        <?php if ($u['description']): ?><div class="timeline__body" style="white-space:pre-line"><?php echo e($u['description']); ?></div><?php endif; ?>

        <?php if (!empty($images)): ?>
          <div class="gallery">
            <?php foreach ($images as $img): ?>
              <div class="gallery__item"><img src="<?php echo e($img); ?>" alt="Construction photo" loading="lazy" data-lightbox></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="timeline__details">
          <?php if ($u['materials_used']): ?><span class="detail-chip"><i class="fa-solid fa-layer-group"></i> <?php echo e($u['materials_used']); ?></span><?php endif; ?>
          <?php if ($u['labor_count'] !== null): ?><span class="detail-chip"><i class="fa-solid fa-user-group"></i> <?php echo (int)$u['labor_count']; ?> workers</span><?php endif; ?>
          <?php if ($u['weather_condition']): ?><span class="detail-chip"><i class="fa-solid fa-cloud-sun"></i> <?php echo e($u['weather_condition']); ?></span><?php endif; ?>
        </div>

        <?php if ($u['next_day_plan']): ?>
          <div class="mt-1 small" style="color:var(--color-info)">
            <i class="fa-solid fa-forward"></i> <strong>Next day plan:</strong> <?php echo e($u['next_day_plan']); ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
  <button class="lightbox__close" id="lightboxClose"><i class="fa-solid fa-xmark"></i></button>
  <img id="lightboxImg" src="" alt="Enlarged photo">
</div>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>