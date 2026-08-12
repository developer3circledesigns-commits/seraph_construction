<?php
/**
 * Admin dashboard.
 */
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');
$isSuper = Auth::isSuper($user);

$stats = Project::stats();
$projects = Project::allForAdmin((int)$user['id'], $isSuper, ['limit' => 6]);

// Non-super admins only see updates for the projects they are assigned to.
if ($isSuper) {
    $recentUpdates = Database::all(
        "SELECT u.*, p.name AS project_name, c.company_name
           FROM daily_updates u
           JOIN projects p ON p.id = u.project_id
           JOIN clients c ON c.id = p.client_id
          ORDER BY u.created_at DESC
          LIMIT 8"
    );
} else {
    $recentUpdates = Database::all(
        "SELECT u.*, p.name AS project_name, c.company_name
           FROM daily_updates u
           JOIN projects p ON p.id = u.project_id
           JOIN clients c ON c.id = p.client_id
           JOIN admin_projects ap ON ap.project_id = p.id
          WHERE ap.admin_id = :admin_id
          ORDER BY u.created_at DESC
          LIMIT 8",
        [':admin_id' => (int)$user['id']]
    );
}

$title = 'Dashboard';
$active = 'dashboard';
include __DIR__ . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1>Welcome, <?php echo e($user['full_name']); ?></h1>
    <p>Here's what's happening across your construction projects today.</p>
  </div>
  <div class="flex">
    <span class="live-dot" id="liveIndicator">Live</span>
    <a href="/admin/projects/create" class="btn btn--primary">
      <i class="fa-solid fa-circle-plus"></i> New Project
    </a>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card stat-card--gold">
    <div class="stat-card__label">Total Projects</div>
    <div class="stat-card__value"><?php echo $stats['total']; ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">In Progress</div>
    <div class="stat-card__value"><?php echo $stats['active']; ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">Completed</div>
    <div class="stat-card__value"><?php echo $stats['completed']; ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">On Hold</div>
    <div class="stat-card__value"><?php echo $stats['on_hold']; ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">Clients</div>
    <div class="stat-card__value"><?php echo $stats['clients']; ?></div>
  </div>
  <!-- <div class="stat-card">
    <div class="stat-card__label">Updates (30d)</div>
    <div class="stat-card__value"><?php echo $stats['updates30']; ?></div>
  </div> -->
</div>

<div class="grid-2">
  <div>
    <div class="card">
      <div class="card__header">
        <h2 class="card__title">Your Projects</h2>
        <a href="/admin/projects" class="btn btn--ghost btn--sm">View all</a>
      </div>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Project</th>
              <th>Client</th>
              <th>Status</th>
              <th>Progress</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($projects)): ?>
            <tr><td colspan="5" class="text-center muted">No projects found.</td></tr>
          <?php endif; ?>
          <?php foreach ($projects as $p): ?>
            <tr>
              <td><strong><?php echo e($p['name']); ?></strong></td>
              <td class="muted"><?php echo e($p['company_name'] ?: $p['contact_person']); ?></td>
              <td><span class="badge badge--<?php echo e($p['status']); ?>"><?php echo e(str_replace('_', ' ', $p['status'])); ?></span></td>
              <td>
                <div class="flex" style="min-width:110px">
                  <div class="progress progress--sm" style="flex:1"><div class="progress__bar" style="width:<?php echo (int)$p['progress_percentage']; ?>%"></div></div>
                  <span class="small muted"><?php echo (int)$p['progress_percentage']; ?>%</span>
                </div>
              </td>
              <td><a class="btn btn--secondary btn--sm" href="/admin/projects/view?id=<?php echo (int)$p['id']; ?>" aria-label="View project <?php echo e($p['name']); ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card__header">
        <h2 class="card__title">Recent Updates</h2>
      </div>
      <?php if (empty($recentUpdates)): ?>
        <p class="muted small">No updates posted yet.</p>
      <?php endif; ?>
      <?php foreach ($recentUpdates as $u): ?>
        <a href="/admin/projects/view?id=<?php echo (int)$u['project_id']; ?>" class="dashboard-update-row" style="display:block;padding:10px 0;border-bottom:1px solid var(--color-border);text-decoration:none;color:inherit">
          <div class="flex flex--between">
            <strong class="small"><?php echo e($u['project_name']); ?></strong>
            <span class="badge badge--<?php echo e($u['status']); ?>"><?php echo e(str_replace('_', ' ', $u['status'])); ?></span>
          </div>
          <div class="small mt-1"><?php echo e($u['title']); ?></div>
          <div class="small muted mt-1">
            <i class="fa-solid fa-calendar-day"></i> <?php echo e(date('d M Y', strtotime($u['update_date']))); ?>
            &middot; <?php echo e(time_ago($u['created_at'])); ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
