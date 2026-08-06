<?php
/**
 * Client portal — dashboard (my projects).
 */
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::CLIENT, '/client/login');
$projects = Project::allForClient((int)$user['id']);

$title = 'My Projects';
$active = 'dashboard';
include __DIR__ . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="welcome">
  <h1>Hello, <?php echo e($user['contact_person']); ?> 👋</h1>
  <p class="muted">Here are the latest updates for your projects with Seraph Build Construction.</p>
</div>

<?php if (empty($projects)): ?>
  <div class="card mt-3">
    <div class="empty-state">
      <i class="fa-solid fa-hard-hat"></i>
      No projects assigned to your account yet.
      <div class="mt-2 muted small">If you've given us a project, contact your Seraph representative to link it to your account.</div>
    </div>
  </div>
<?php endif; ?>

<div class="project-grid mt-3">
  <?php foreach ($projects as $p): ?>
    <a class="project-card" href="/client/projects/view?id=<?php echo (int)$p['id']; ?>">
      <div class="project-card__top">
        <h2 class="project-card__name"><?php echo e($p['name']); ?></h2>
        <span class="badge badge--<?php echo e($p['status']); ?>"><?php echo e(str_replace('_', ' ', $p['status'])); ?></span>
      </div>

      <?php if ($p['description']): ?>
        <div class="muted small" style="flex:1"><?php echo e(mb_strimwidth($p['description'], 0, 110, '…')); ?></div>
      <?php else: ?>
        <div class="flex" style="flex:1"></div>
      <?php endif; ?>

      <div class="project-card__bottom">
        <div class="project-card__progress">
          <?php
            $ring = (int)$p['progress_percentage'];
            $deg = $ring * 3.6;
          ?>
          <div class="progress-ring" style="--size:58px;--stroke:6px;background:conic-gradient(var(--color-gold) <?php echo $deg; ?>deg, var(--color-border) 0deg)">
            <div class="progress-ring__inner"><?php echo $ring; ?>%</div>
          </div>
        </div>
        <div class="project-card__meta">
          <?php if ($p['last_update_date']): ?>
            <?php
              $ago = (strtotime($p['last_update_date']) === strtotime(date('Y-m-d')))
                  ? 'today' : date('d M', strtotime($p['last_update_date']));
            ?>
            <span><i class="fa-solid fa-circle-check" style="color:var(--color-success)"></i> Updated <?php echo e($ago); ?></span>
          <?php else: ?>
            <span class="muted">No updates yet</span>
          <?php endif; ?>
          <span class="muted"><?php echo (int)$p['update_count']; ?> update(s)</span>
        </div>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<div class="card mt-3" style="border-color:var(--color-gold)">
  <div class="card__body muted small">
    <i class="fa-solid fa-circle-info" style="color:var(--color-gold)"></i>
    Updates are posted live by our team. The <strong class="muted">Live</strong> indicator above turns green when you're connected for real-time updates.
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>