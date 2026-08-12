<?php
/**
 * Admin — Contact form enquiries (list).
 */
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$search = trim((string)($_GET['search'] ?? ''));
$dbError = null;
$inquiries = [];

try {
    $inquiries = ContactInquiry::all($search !== '' ? $search : null);
} catch (Throwable $e) {
    error_log('Admin contact inquiries list failed: ' . $e->getMessage());
    $dbError = 'Could not load enquiries. Please check the database connection and try again.';
}

$total = count($inquiries);

$title = 'Contact Enquiries';
$active = 'contact_inquiries';
include __DIR__ . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1>Contact Enquiries</h1>
    <p><?php echo $total; ?> inquiry<?php echo $total !== 1 ? 's' : ''; ?> received.</p>
  </div>

  <div class="flex">
    <a href="/admin" class="btn btn--ghost"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>

<?php if ($dbError): ?>
  <div class="alert alert--error" role="alert"><?php echo e($dbError); ?></div>
<?php endif; ?>

<div class="card">
  <div class="card__header">
    <h2>Search Inquiries</h2>
    <p class="muted small">Filter by name, email, phone, or service type.</p>
  </div>

  <form method="GET" action="/admin/contact-inquiries" class="filter-form" style="max-width:400px">
    <div class="form-group">
      <label class="form-label" for="search">Search</label>
      <input class="form-control" type="text" id="search" name="search" placeholder="Search by name, email, phone..." value="<?php echo e($search); ?>">
    </div>
    <button class="btn btn--primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
    <a href="/admin/contact-inquiries" class="btn btn--secondary" style="margin-left:8px">Clear</a>
  </form>
</div>

<div class="card">
  <div class="card__header">
    <h2>Inquiries</h2>
    <p class="muted small">View all contact form submissions.</p>
  </div>

  <?php if ($dbError): ?>
    <div class="empty-state">
      <i class="fa-solid fa-triangle-exclamation"></i> <span>Enquiries could not be loaded.</span>
    </div>
  <?php elseif ($total === 0): ?>
    <div class="empty-state">
      <i class="fa-solid fa-envelope-opened"></i> <span>No inquiries yet.</span>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Client</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Service</th>
            <th>Date</th>
            <th style="width:180px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($inquiries as $i): ?>
            <tr>
              <td><strong><?php echo e($i['full_name'] ?: '—'); ?></strong></td>
              <td><a href="mailto:<?php echo e($i['email']); ?>"><?php echo e(strlen($i['email']) > 30 ? substr($i['email'], 0, 30) . '...' : $i['email']); ?></a></td>
              <td class="small"><?php echo e($i['phone']); ?></td>
              <td class="small"><?php echo e(ContactInquiry::serviceLabel($i['service_type'] ?? null)); ?></td>
              <td class="small muted"><?php echo e($i['query_date'] ?? date('Y-m-d', strtotime((string)$i['created_at']))); ?></td>
              <td style="white-space:nowrap">
                <a class="btn btn--secondary btn--sm" href="/admin/contact-inquiry-view?id=<?php echo (int)$i['id']; ?>" aria-label="View inquiry #<?php echo (int)$i['id']; ?>"><i class="fa-solid fa-eye"></i> View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
