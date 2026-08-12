<?php
/**
 * Admin — view a single contact form enquiry.
 */
declare(strict_types=1);
require dirname(__DIR__, 2) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$id = (int)($_GET['id'] ?? 0);
$inquiry = Database::one('SELECT * FROM contact_inquiries WHERE id = :id', [':id' => $id]);
if (!$inquiry) {
    redirect('/admin/contact-inquiries', 'Inquiry not found.', 'error');
}

$serviceLabels = [
    'construction'    => 'Construction',
    'interior'        => 'Interior Design',
    'modular_kitchen' => 'Modular Kitchen',
    'renovation'      => 'Renovation',
    'commercial'      => 'Commercial',
    'other'           => 'Other',
];

$serviceLabel = $inquiry['service_type']
    ? ($serviceLabels[$inquiry['service_type']] ?? ucfirst((string)$inquiry['service_type']))
    : '—';

$title = 'Inquiry #' . $id;
$active = 'contact_inquiries';
include dirname(__DIR__) . '/partials/header.php';
?>
<?php echo flash(); ?>

<div class="page-header">
  <div>
    <h1>Inquiry #<?php echo (int)$inquiry['id']; ?></h1>
    <p>Submitted <?php echo e(date('d M Y, h:i A', strtotime((string)$inquiry['created_at']))); ?></p>
  </div>
  <div class="flex">
    <a href="/admin/contact-inquiries" class="btn btn--ghost"><i class="fa-solid fa-arrow-left"></i> All Enquiries</a>
    <a href="mailto:<?php echo e($inquiry['email']); ?>" class="btn btn--primary"><i class="fa-solid fa-reply"></i> Reply by Email</a>
  </div>
</div>

<div class="card">
  <div class="card__header">
    <h2>Contact Details</h2>
  </div>
  <div class="small">
    <div class="flex flex--between mb-1"><span class="muted">Full Name</span><strong><?php echo e($inquiry['full_name']); ?></strong></div>
    <div class="flex flex--between mb-1"><span class="muted">Email</span><a href="mailto:<?php echo e($inquiry['email']); ?>"><?php echo e($inquiry['email']); ?></a></div>
    <div class="flex flex--between mb-1"><span class="muted">Phone</span><a href="tel:<?php echo e(preg_replace('/\D/', '', (string)$inquiry['phone'])); ?>"><?php echo e($inquiry['phone']); ?></a></div>
    <div class="flex flex--between mb-1"><span class="muted">Service Type</span><strong><?php echo e($serviceLabel); ?></strong></div>
    <div class="flex flex--between mb-1"><span class="muted">Submitted</span><strong><?php echo e(date('d M Y, h:i A', strtotime((string)$inquiry['created_at']))); ?></strong></div>
    <?php if (!empty($inquiry['ip_address'])): ?>
    <div class="flex flex--between mb-1"><span class="muted">IP Address</span><span><?php echo e($inquiry['ip_address']); ?></span></div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card__header">
    <h2>Message</h2>
  </div>
  <p class="small" style="white-space:pre-wrap;line-height:1.7"><?php echo e($inquiry['message']); ?></p>
</div>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
