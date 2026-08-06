<?php
/**
 * Admin — edit client.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$id = (int)($_GET['id'] ?? 0);
$client = Database::one('SELECT * FROM clients WHERE id = :id', [':id' => $id]);
if (!$client) redirect('/admin/clients', 'Client not found.', 'error');

$errors = [];
$old = $client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (!filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($body['contact_person'])) $errors[] = 'Contact person name is required.';

    $dupe = Database::scalar('SELECT 1 FROM clients WHERE email = :e AND id <> :id', [':e' => $body['email'], ':id' => $id]);
    if ($dupe) $errors[] = 'Another client already uses this email.';

    // Validate password reset BEFORE any update so we don't partially save.
    $newPassword = (string)($body['new_password'] ?? '');
    if ($newPassword !== '' && strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }

    if (!$errors) {
        Database::execute(
            "UPDATE clients SET email = :email, company_name = :company, contact_person = :contact,
                    phone = :phone, address = :address, is_active = :active
              WHERE id = :id",
            [
                ':email'    => $body['email'],
                ':company'  => $body['company_name'] ?? null,
                ':contact'  => $body['contact_person'],
                ':phone'    => $body['phone'] ?? null,
                ':address'  => $body['address'] ?? null,
                ':active'   => !empty($body['is_active']) ? 1 : 0,
                ':id'       => $id,
            ]
        );

        // Optional password reset
        if ($newPassword !== '' && !$errors) {
            Database::execute('UPDATE clients SET password_hash = :h WHERE id = :id', [
                ':h' => Auth::hash($newPassword), ':id' => $id,
            ]);
        }

        if (!$errors) {
            Audit::admin((int)$user['id'], 'client_update', 'client', $id);
            redirect('/admin/clients', 'Client updated successfully.');
        }
    }
}

// Client's projects
$projects = Database::all(
    "SELECT * FROM projects WHERE client_id = :id ORDER BY updated_at DESC",
    [':id' => $id]
);

$title = 'Edit Client';
$active = 'clients';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div>
    <h1><?php echo e($old['company_name'] ?: $old['contact_person']); ?></h1>
    <p>Edit client details and portal credentials.</p>
  </div>
</div>

<form method="POST" action="/admin/clients/edit?id=<?php echo (int)$id; ?>">
  <?php echo CSRF::field(); ?>
  <div class="card" style="max-width:640px">
    <div class="form-group">
      <label class="form-label" for="company_name">Company / Organisation</label>
      <input class="form-control" type="text" id="company_name" name="company_name" value="<?php echo e($old['company_name']); ?>">
    </div>
    <div class="form-group">
      <label class="form-label" for="contact_person">Contact Person *</label>
      <input class="form-control" type="text" id="contact_person" name="contact_person" required value="<?php echo e($old['contact_person']); ?>">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="email">Email (login) *</label>
        <input class="form-control" type="email" id="email" name="email" required value="<?php echo e($old['email']); ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone</label>
        <input class="form-control" type="text" id="phone" name="phone" value="<?php echo e($old['phone']); ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="address">Address</label>
      <textarea class="form-control" id="address" name="address"><?php echo e($old['address']); ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label" for="new_password">Reset Password (leave blank to keep)</label>
      <input class="form-control" type="text" id="new_password" name="new_password" minlength="8" value="" placeholder="New password (min 8 chars)" autocomplete="off">
    </div>
    <div class="form-group">
      <label class="flex" style="cursor:pointer;gap:10px">
        <input type="checkbox" name="is_active" value="1" style="accent-color:var(--color-gold);width:16px;height:16px"
          <?php echo $old['is_active'] ? 'checked' : ''; ?>>
        <span><strong>Account active</strong> <span class="muted small">(client can log in)</span></span>
      </label>
    </div>
  </div>
  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Save Changes</button>
    <a href="/admin/clients" class="btn btn--ghost">Cancel</a>
  </div>
</form>

<div class="card mt-3">
  <div class="card__header">
    <h2 class="card__title">Client Projects</h2>
    <a href="/admin/projects/create" class="btn btn--primary btn--sm"><i class="fa-solid fa-plus"></i> Add Project</a>
  </div>
  <?php if (empty($projects)): ?>
    <p class="muted small">No projects assigned to this client yet.</p>
  <?php endif; ?>
  <?php foreach ($projects as $p): ?>
    <div class="flex flex--between" style="padding:10px 0;border-bottom:1px solid var(--color-border)">
      <div>
        <strong><?php echo e($p['name']); ?></strong>
        <div class="small muted"><?php echo e($p['location'] ?: ''); ?></div>
      </div>
      <div class="flex">
        <span class="badge badge--<?php echo e($p['status']); ?>"><?php echo e(str_replace('_', ' ', $p['status'])); ?></span>
        <a class="btn btn--secondary btn--sm" href="/admin/projects/view?id=<?php echo (int)$p['id']; ?>" aria-label="View project <?php echo e($p['name']); ?>">View</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
