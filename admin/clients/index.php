<?php
/**
 * Admin — client list.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login.php');

$search = trim((string)($_GET['search'] ?? ''));
if ($search !== '') {
    $clients = Database::all(
        "SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.client_id = c.id) AS project_count
           FROM clients c
          WHERE c.company_name LIKE :q1 OR c.contact_person LIKE :q2 OR c.email LIKE :q3
          ORDER BY c.created_at DESC",
        [':q1' => '%' . $search . '%', ':q2' => '%' . $search . '%', ':q3' => '%' . $search . '%']
    );
} else {
    $clients = Database::all(
        "SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.client_id = c.id) AS project_count
           FROM clients c
          ORDER BY c.created_at DESC"
    );
}

$title = 'Clients';
$active = 'clients';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1>Clients</h1>
    <p><?php echo count($clients); ?> client(s) registered.</p>
  </div>
  <a href="/admin/clients/create.php" class="btn btn--primary"><i class="fa-solid fa-user-plus"></i> New Client</a>
</div>

<div class="filters">
  <form method="GET" action="/admin/clients/index.php" class="flex" style="gap:12px;width:100%">
    <input class="form-control" type="search" name="search" placeholder="Search by name, company or email..." value="<?php echo e($search); ?>" style="flex:1;min-width:220px">
    <button class="btn btn--secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
  </form>
</div>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Client</th>
        <th>Contact</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Projects</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($clients)): ?>
      <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-users-slash"></i>No clients found.</div></td></tr>
    <?php endif; ?>
    <?php foreach ($clients as $c): ?>
      <tr>
        <td><strong><?php echo e($c['company_name'] ?: $c['contact_person']); ?></strong></td>
        <td><?php echo e($c['contact_person']); ?></td>
        <td class="small"><?php echo e($c['email']); ?></td>
        <td class="small muted"><?php echo e($c['phone'] ?: '—'); ?></td>
        <td class="muted"><?php echo (int)$c['project_count']; ?></td>
        <td>
          <span class="badge <?php echo $c['is_active'] ? 'badge--in_progress' : 'badge--cancelled'; ?>">
            <?php echo $c['is_active'] ? 'Active' : 'Inactive'; ?>
          </span>
        </td>
        <td style="white-space:nowrap">
          <a class="btn btn--secondary btn--sm" href="/admin/clients/edit.php?id=<?php echo (int)$c['id']; ?>" aria-label="Edit client <?php echo e($c['company_name'] ?: $c['contact_person']); ?>"><i class="fa-solid fa-pen"></i> Edit</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
