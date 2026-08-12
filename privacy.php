<?php
/**
 * SERAPH BUILD CONSTRUCTION — Privacy Policy.
 */
declare(strict_types=1);

$site = require __DIR__ . '/config/site.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main-content" class="contact-page">
  <div class="contact-page__head">
    <span class="contact-page__eyebrow">Legal</span>
    <h1>Privacy Policy</h1>
    <p>How Seraph Build Construction collects, uses, and protects your information.</p>
  </div>

  <div class="contact-page__form-wrap" style="max-width:720px">
    <div class="contact-alert contact-alert--info" role="note">
      <p>Last updated: <?php echo date('F j, Y'); ?></p>
    </div>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Information we collect</h2>
    <p style="color:var(--muted);line-height:1.7">When you submit our contact or quote form, we collect the details you provide (name, email, phone, service interest, and project message). We also record the submission time and IP address for abuse prevention.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">How we use it</h2>
    <p style="color:var(--muted);line-height:1.7">We use your information solely to respond to your enquiry, provide quotations, and deliver our construction and design services. We do not sell your personal data to third parties.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Data retention</h2>
    <p style="color:var(--muted);line-height:1.7">Contact enquiries are retained in our secure systems for as long as needed to manage your request and comply with applicable business and legal requirements.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Your rights</h2>
    <p style="color:var(--muted);line-height:1.7">You may request access to, correction of, or deletion of your personal data by contacting us at <a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a>.</p>

    <h2 style="font-family:var(--display);color:var(--white);margin:1.5rem 0 0.75rem;font-size:1.25rem">Contact</h2>
    <p style="color:var(--muted);line-height:1.7"><?php echo $site['name']; ?><br><?php echo $site['address']; ?><br>Phone: <a href="tel:<?php echo e($site['phone_tel']); ?>"><?php echo e($site['phone']); ?></a></p>

    <p style="margin-top:2rem"><a href="<?php echo e($site['contact_url'] ?? 'contact.php'); ?>" class="btn btn--gold">Contact Us</a></p>
  </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
