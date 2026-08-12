<?php
/**
 * SERAPH BUILD CONSTRUCTION — Terms & Conditions.
 */
declare(strict_types=1);

$site = require __DIR__ . '/config/site.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main-content" class="contact-page">
  <div class="contact-page__head">
    <span class="contact-page__eyebrow">Legal</span>
    <h1>Terms &amp; Conditions</h1>
    <p>Terms governing use of this website and our services.</p>
  </div>

  <div class="contact-page__form-wrap" style="max-width:720px">
    <div class="contact-alert contact-alert--info" role="note">
      <p>Last updated: <?php echo date('F j, Y'); ?></p>
    </div>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Website use</h2>
    <p style="color:var(--muted);line-height:1.7">This website is operated by <?php echo e($site['name']); ?>. Content is provided for general information about our construction, interior, and renovation services. We reserve the right to update site content at any time.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Quotations &amp; projects</h2>
    <p style="color:var(--muted);line-height:1.7">Information submitted through our contact form does not constitute a binding contract. Formal project scope, timelines, and pricing are confirmed only in signed agreements between you and Seraph Build Construction.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Intellectual property</h2>
    <p style="color:var(--muted);line-height:1.7">All designs, images, text, and branding on this site remain the property of Seraph Build Construction unless otherwise agreed in writing.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Limitation of liability</h2>
    <p style="color:var(--muted);line-height:1.7">We strive to keep this website accurate and available, but we do not guarantee uninterrupted access. To the extent permitted by law, we are not liable for indirect losses arising from use of this website.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Governing law</h2>
    <p style="color:var(--muted);line-height:1.7">These terms are governed by the laws of India. Disputes shall be subject to the courts of Chennai, Tamil Nadu.</p>

    <p style="margin-top:2rem"><a href="<?php echo e($site['contact_url'] ?? 'contact.php'); ?>" class="btn btn--gold">Contact Us</a></p>
  </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
