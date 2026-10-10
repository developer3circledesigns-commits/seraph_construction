<?php
/**
 * Admin — view a single contact form enquiry.
 */
declare(strict_types=1);
require dirname(__DIR__) . '/api/config/bootstrap.php';

$user = Auth::requireUser(Auth::ADMIN, '/admin/login');

$id = (int)($_GET['id'] ?? 0);
$inquiry = null;

try {
    $inquiry = ContactInquiry::find($id);
} catch (Throwable $e) {
    error_log('Admin contact inquiry view failed: ' . $e->getMessage());
    redirect('/admin/contact-inquiries', 'Could not load that enquiry. Please try again.', 'error');
}

if (!$inquiry) {
    redirect('/admin/contact-inquiries', 'Inquiry not found.', 'error');
}

$title = 'Inquiry #' . $id;
$active = 'contact_inquiries';
include __DIR__ . '/partials/header.php';
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

<div class="enquiry-grid">
<div class="card">
  <div class="card__header">
    <h2 class="card__title">Contact Details</h2>
  </div>
  <div class="detail-list small">
    <div class="flex"><span class="detail-list__label">Full Name</span><strong class="detail-list__value"><?php echo e($inquiry['full_name']); ?></strong></div>
    <div class="flex"><span class="detail-list__label">Email</span><a class="detail-list__value" href="mailto:<?php echo e($inquiry['email']); ?>"><?php echo e($inquiry['email']); ?></a></div>
    <div class="flex"><span class="detail-list__label">Phone</span><a class="detail-list__value" href="tel:<?php echo e(preg_replace('/\D/', '', (string)$inquiry['phone'])); ?>"><?php echo e($inquiry['phone']); ?></a></div>
    <div class="flex"><span class="detail-list__label">Service Type</span><strong class="detail-list__value"><?php echo e(ContactInquiry::serviceLabel($inquiry['service_type'] ?? null)); ?></strong></div>
    <div class="flex"><span class="detail-list__label">Submitted</span><strong class="detail-list__value"><?php echo e(date('d M Y, h:i A', strtotime((string)$inquiry['created_at']))); ?></strong></div>
    <?php if (!empty($inquiry['ip_address'])): ?>
    <div class="flex"><span class="detail-list__label">IP Address</span><span class="detail-list__value"><?php echo e($inquiry['ip_address']); ?></span></div>
    <?php endif; ?>
  </div>
</div>

<?php $calcView = !empty($inquiry['calc_json']) ? json_decode((string)$inquiry['calc_json'], true) : null; ?>
<?php if (is_array($calcView)): ?>
<div class="card">
  <div class="card__header">
    <h2 class="card__title">Cost Calculator Estimate</h2>
  </div>
  <div class="detail-list small">
    <div class="flex"><span class="detail-list__label">Package</span><strong class="detail-list__value"><?php echo e(ucfirst((string)$calcView['package'])); ?> @ ₹<?php echo inr_format($calcView['rate']); ?>/sqft</strong></div>
    <?php foreach ((array)($calcView['floors'] ?? []) as $f): ?>
      <div class="flex"><span class="detail-list__label"><?php echo e($f['label']); ?></span><strong class="detail-list__value"><?php echo e((string)$f['area']); ?> sqft</strong></div>
    <?php endforeach; ?>
    <div class="flex"><span class="detail-list__label">RCC Water Sump</span><strong class="detail-list__value"><?php echo e((string)$calcView['sump_ltr']); ?> ltr</strong></div>
    <div class="flex"><span class="detail-list__label">Septic Tank</span><strong class="detail-list__value"><?php echo e((string)$calcView['septic_ltr']); ?> ltr</strong></div>
    <div class="flex"><span class="detail-list__label">Compound Wall</span><strong class="detail-list__value"><?php echo e((string)$calcView['wall_l']); ?> × <?php echo e((string)$calcView['wall_h']); ?> sqft</strong></div>
    <div class="flex"><span class="detail-list__label"><strong>Estimated Total</strong></span><strong class="detail-list__value">₹<?php echo inr_format($calcView['total']); ?></strong></div>
  </div>
</div>
<?php endif; ?>

<div class="card enquiry-card--wide">
  <div class="card__header">
    <h2 class="card__title">Message</h2>
  </div>
  <p class="small" style="white-space:pre-wrap;line-height:1.7;overflow-wrap:anywhere"><?php echo e(trim((string)($inquiry['message'] ?? '')) !== '' ? $inquiry['message'] : '(Not provided)'); ?></p>
</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
