<?php
/**
 * Admin — Contact form enquiries.
 */
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$search = trim((string)($_GET['search'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));

$query = "SELECT ci.*, DATE(ci.created_at) as query_date FROM contact_inquiries ci ORDER BY ci.created_at DESC";

if ($search !== '') {
    $query = "SELECT ci.*, DATE(ci.created_at) as query_date FROM contact_inquiries ci WHERE ci.full_name LIKE :q OR ci.email LIKE :q OR ci.phone LIKE :q OR ci.service_type LIKE :q ORDER BY ci.created_at DESC";
}

$inquiries = Database::all($query);
$total = count($inquiries);

$title = 'Contact Enquiries';
$active = 'dashboard';
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

<!-- Filter Section Moved Above Enquiries -->
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

  <?php if ($total === 0): ?>
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
              <td><a href="mailto:<?php echo e($i['email']); ?>"><?php echo e(substr($i['email'], 0, 30) . (strlen($i['email']) > 30 ? '...' : '')); ?></a></td>
              <td class="small"><?php echo e($i['phone']); ?></td>
              <td class="small">
                <?php echo $i['service_type'] !== '' ? ucfirst($i['service_type']) : '—'; ?>
              </td>
              <td class="small muted"><?php echo e($i['query_date']); ?></td>
              <td style="white-space:nowrap">
                <a class="btn btn--secondary btn--sm" href="/admin/contact-inquiries/view?id=<?php echo (int)$i['id']; ?>" aria-label="View inquiry #<?php echo (int)$i['id']; ?>"><i class="fa-solid fa-eye"></i> View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card__header">
    <h2>View Inquiry #<?php echo isset($_GET['id']) ? (int)$_GET['id'] : '—'; ?></h2>
  </div>
  <?php if (isset($_GET['id']) && (int)$_GET['id'] > 0): ?>
    <div class="table-wrap">
      <p>Inquiry details for ID <strong><?php echo (int)$_GET['id']; ?></strong>. <a href="/admin/contact-inquiries">Back to list</a>.</p>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>