<?php
/**
 * Admin — create a client account.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login.php');

$errors = [];
$old = [
    'email'          => '',
    'company_name'   => '',
    'contact_person' => '',
    'phone'          => '',
    'address'        => '',
    'password'       => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validate();
    $body = request_body();
    $old = array_merge($old, $body);

    if (!filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($body['contact_person'])) $errors[] = 'Contact person name is required.';
    if (strlen($body['password'] ?? '') < 8) $errors[] = 'Password must be at least 8 characters.';

    if (Database::scalar('SELECT 1 FROM clients WHERE email = :e', [':e' => $body['email']])) {
        $errors[] = 'A client with this email already exists.';
    }

    if (!$errors) {
        $clientId = Database::insert(
            "INSERT INTO clients (email, password_hash, company_name, contact_person, phone, address)
             VALUES (:email, :hash, :company, :contact, :phone, :address)",
            [
                ':email'   => $body['email'],
                ':hash'    => Auth::hash($body['password']),
                ':company' => $body['company_name'] ?? null,
                ':contact' => $body['contact_person'],
                ':phone'   => $body['phone'] ?? null,
                ':address' => $body['address'] ?? null,
            ]
        );
        Audit::admin((int)$user['id'], 'client_create', 'client', $clientId, ['email' => $body['email']]);
        redirect('/admin/clients/edit.php?id=' . $clientId, 'Client account created. Now create a project for them.');
    }
}

$title = 'New Client';
$active = 'clients';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php foreach ($errors as $err): ?><div class="alert alert--error"><?php echo e($err); ?></div><?php endforeach; ?>

<div class="page-header">
  <div><h1>New Client</h1><p>Create a portal account for a client.</p></div>
</div>

<form method="POST" action="/admin/clients/create.php">
  <?php echo CSRF::field(); ?>
  <div class="card" style="max-width:640px">
    <div class="form-group">
      <label class="form-label" for="company_name">Company / Organisation</label>
      <input class="form-control" type="text" id="company_name" name="company_name" value="<?php echo e($old['company_name']); ?>" placeholder="e.g. Azure Enterprises">
    </div>
    <div class="form-group">
      <label class="form-label" for="contact_person">Contact Person *</label>
      <input class="form-control" type="text" id="contact_person" name="contact_person" required value="<?php echo e($old['contact_person']); ?>" placeholder="e.g. Rajesh Kumar">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="email">Email (login) *</label>
        <input class="form-control" type="email" id="email" name="email" required value="<?php echo e($old['email']); ?>" placeholder="client@example.com" autocomplete="off">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone</label>
        <input class="form-control" type="text" id="phone" name="phone" value="<?php echo e($old['phone']); ?>" placeholder="+91 90000 00000">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="address">Address</label>
      <textarea class="form-control" id="address" name="address"><?php echo e($old['address']); ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label" for="password">Portal Password *</label>
      <input class="form-control" type="text" id="password" name="password" required minlength="8" value="<?php echo e($old['password']); ?>" placeholder="Min 8 characters" autocomplete="off">
      <div class="form-help">Share this password with the client securely — they'll use it with their email to log in to the client portal.</div>
    </div>
  </div>
  <div class="flex mt-2">
    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-check"></i> Create Client</button>
    <a href="/admin/clients/index.php" class="btn btn--ghost">Cancel</a>
  </div>
</form>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
