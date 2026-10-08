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

<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:stretch">
<div class="card" style="flex:1 1 260px;padding:14px 16px;margin-top:0">
  <div class="card__header">
    <h2>Contact Details</h2>
  </div>
  <div class="small">
    <div class="flex flex--between mb-1"><span class="muted">Full Name</span><strong><?php echo e($inquiry['full_name']); ?></strong></div>
    <div class="flex flex--between mb-1"><span class="muted">Email</span><a href="mailto:<?php echo e($inquiry['email']); ?>"><?php echo e($inquiry['email']); ?></a></div>
    <div class="flex flex--between mb-1"><span class="muted">Phone</span><a href="tel:<?php echo e(preg_replace('/\D/', '', (string)$inquiry['phone'])); ?>"><?php echo e($inquiry['phone']); ?></a></div>
    <div class="flex flex--between mb-1"><span class="muted">Service Type</span><strong><?php echo e(ContactInquiry::serviceLabel($inquiry['service_type'] ?? null)); ?></strong></div>
    <div class="flex flex--between mb-1"><span class="muted">Submitted</span><strong><?php echo e(date('d M Y, h:i A', strtotime((string)$inquiry['created_at']))); ?></strong></div>
    <?php if (!empty($inquiry['ip_address'])): ?>
    <div class="flex flex--between mb-1"><span class="muted">IP Address</span><span><?php echo e($inquiry['ip_address']); ?></span></div>
    <?php endif; ?>
  </div>
</div>

<?php $calcView = !empty($inquiry['calc_json']) ? json_decode((string)$inquiry['calc_json'], true) : null; ?>
<?php if (is_array($calcView)): ?>
<div class="card" style="flex:1 1 260px;padding:14px 16px;margin-top:0">
  <div class="card__header">
    <h2>Cost Calculator Estimate</h2>
  </div>
  <div class="small">
    <div class="flex flex--between mb-1"><span class="muted">Package</span><strong><?php echo e(ucfirst((string)$calcView['package'])); ?> @ ₹<?php echo inr_format($calcView['rate']); ?>/sqft</strong></div>
    <?php foreach ((array)($calcView['floors'] ?? []) as $f): ?>
      <div class="flex flex--between mb-1"><span class="muted"><?php echo e($f['label']); ?></span><strong><?php echo e((string)$f['area']); ?> sqft</strong></div>
    <?php endforeach; ?>
    <div class="flex flex--between mb-1"><span class="muted">RCC Water Sump</span><strong><?php echo e((string)$calcView['sump_ltr']); ?> ltr</strong></div>
    <div class="flex flex--between mb-1"><span class="muted">Septic Tank</span><strong><?php echo e((string)$calcView['septic_ltr']); ?> ltr</strong></div>
    <div class="flex flex--between mb-1"><span class="muted">Compound Wall</span><strong><?php echo e((string)$calcView['wall_l']); ?> × <?php echo e((string)$calcView['wall_h']); ?> sqft</strong></div>
    <div class="flex flex--between mb-1"><span class="muted"><strong>Estimated Total</strong></span><strong>₹<?php echo inr_format($calcView['total']); ?></strong></div>
  </div>
</div>
<?php endif; ?>

<div class="card" style="flex:2 1 320px;padding:14px 16px;margin-top:0">
  <div class="card__header">
    <h2>Message</h2>
  </div>
  <p class="small" style="white-space:pre-wrap;line-height:1.7"><?php echo e(trim((string)($inquiry['message'] ?? '')) !== '' ? $inquiry['message'] : '(Not provided)'); ?></p>
</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
