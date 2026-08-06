<?php
/**
 * Admin — projects list with filters + search.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');
$isSuper = Auth::isSuper($user);

$filters = [
    'status'  => $_GET['status'] ?? '',
    'search'  => trim((string)($_GET['search'] ?? '')),
];

$projects = Project::allForAdmin((int)$user['id'], $isSuper, $filters);

$title = 'Projects';
$active = 'projects';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1>Projects</h1>
    <p><?php echo count($projects); ?> project(s) found.</p>
  </div>
  <a href="/admin/projects/create" class="btn btn--primary">
    <i class="fa-solid fa-circle-plus"></i> New Project
  </a>
</div>

<div class="filters">
  <form method="GET" action="/admin/projects" class="flex flex--wrap" style="gap:12px;width:100%">
    <input class="form-control" type="search" name="search" placeholder="Search by name, client..." value="<?php echo e($filters['search']); ?>" style="flex:1;min-width:220px">
    <select class="form-control" name="status" onchange="this.form.submit()">
      <option value="">All statuses</option>
      <?php foreach (['planning','in_progress','on_hold','completed','cancelled'] as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo $filters['status'] === $s ? 'selected' : ''; ?>><?php echo e(ucfirst(str_replace('_', ' ', $s))); ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--secondary" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
  </form>
</div>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Project</th>
        <th>Client</th>
        <th>Location</th>
        <th>Status</th>
        <th>Progress</th>
        <th>Updates</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($projects)): ?>
      <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-building-circle-exclamation"></i>No projects match your filters.</div></td></tr>
    <?php endif; ?>
    <?php foreach ($projects as $p): ?>
      <tr>
        <td>
          <strong><?php echo e($p['name']); ?></strong>
          <?php if ($p['budget']): ?><div class="small muted"><?php echo money($p['budget']); ?></div><?php endif; ?>
        </td>
        <td class="muted"><?php echo e($p['company_name'] ?: $p['contact_person']); ?></td>
        <td class="muted small"><?php echo e($p['location'] ?: '—'); ?></td>
        <td><span class="badge badge--<?php echo e($p['status']); ?>"><?php echo e(str_replace('_', ' ', $p['status'])); ?></span></td>
        <td>
          <div class="flex" style="min-width:110px">
            <div class="progress progress--sm" style="flex:1"><div class="progress__bar" style="width:<?php echo (int)$p['progress_percentage']; ?>%"></div></div>
            <span class="small muted"><?php echo (int)$p['progress_percentage']; ?>%</span>
          </div>
        </td>
        <td class="muted"><?php echo (int)$p['update_count']; ?></td>
        <td style="white-space:nowrap">
          <a class="btn btn--secondary btn--sm" href="/admin/projects/view?id=<?php echo (int)$p['id']; ?>" aria-label="View project <?php echo e($p['name']); ?>"><i class="fa-solid fa-eye"></i></a>
          <a class="btn btn--secondary btn--sm" href="/admin/projects/edit?id=<?php echo (int)$p['id']; ?>" aria-label="Edit project <?php echo e($p['name']); ?>"><i class="fa-solid fa-pen"></i></a>
          <a class="btn btn--secondary btn--sm" href="/admin/projects/assign?id=<?php echo (int)$p['id']; ?>" aria-label="Assign admins to project <?php echo e($p['name']); ?>"><i class="fa-solid fa-user-gear"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
