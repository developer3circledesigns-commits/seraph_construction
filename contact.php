<?php
/**
 * SERAPH BUILD CONSTRUCTION — Contact & quote request page.
 */
declare(strict_types=1);

$site = require __DIR__ . '/config/site.php';
require __DIR__ . '/api/config/bootstrap.php';

$serviceTypes = [
    'construction'    => 'Construction',
    'interior'        => 'Interior Design',
    'modular_kitchen' => 'Modular Kitchen',
    'renovation'      => 'Renovation',
    'commercial'      => 'Commercial',
    'other'           => 'Other',
];

$errors = [];
$old = [
    'full_name'    => '',
    'email'        => '',
    'phone'        => '',
    'service_type' => '',
    'message'      => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = request_body();
    $old = array_merge($old, array_intersect_key($body, $old));

    if (!CSRF::verify($body['_csrf'] ?? null)) {
        $errors[] = 'Your session has expired. Please refresh the page and try again.';
    }

    $fullName = trim((string)($body['full_name'] ?? ''));
    $email = trim((string)($body['email'] ?? ''));
    $phone = trim((string)($body['phone'] ?? ''));
    $serviceType = trim((string)($body['service_type'] ?? ''));
    $message = trim((string)($body['message'] ?? ''));

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    } elseif (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120) {
        $errors[] = 'Full name must be between 2 and 120 characters.';
    }

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    } else {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 15) {
            $errors[] = 'Please enter a valid phone number (10–15 digits).';
        }
    }

    if ($serviceType !== '' && !array_key_exists($serviceType, $serviceTypes)) {
        $errors[] = 'Please select a valid service type.';
    }

    if ($message === '') {
        $errors[] = 'Message is required.';
    } elseif (mb_strlen($message) < 20) {
        $errors[] = 'Message must be at least 20 characters.';
    } elseif (mb_strlen($message) > 5000) {
        $errors[] = 'Message must be 5000 characters or fewer.';
    }

    if (!$errors) {
        $ip = client_ip();
        $recentCount = (int)Database::scalar(
            "SELECT COUNT(*) FROM contact_inquiries
              WHERE ip_address = :ip AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [':ip' => $ip]
        );

        if ($recentCount >= 5) {
            $errors[] = 'Too many requests. Please wait an hour before submitting again.';
        } else {
            try {
                Database::insert(
                    "INSERT INTO contact_inquiries (full_name, email, phone, service_type, message, ip_address)
                     VALUES (:name, :email, :phone, :service, :message, :ip)",
                    [
                        ':name'    => $fullName,
                        ':email'   => $email,
                        ':phone'   => $phone,
                        ':service' => $serviceType !== '' ? $serviceType : null,
                        ':message' => $message,
                        ':ip'      => $ip,
                    ]
                );
                redirect('/contact', 'Thank you! Your message has been sent. Our team will contact you shortly.');
            } catch (Throwable $e) {
                error_log('Contact form insert failed: ' . $e->getMessage());
                $errors[] = 'Unable to send your message right now. Please call us directly or try again later.';
            }
        }
    }
}

require __DIR__ . '/partials/header.php';
?>
<main id="main-content" class="contact-page">
  <div class="contact-page__head">
    <span class="contact-page__eyebrow">Get in Touch</span>
    <h1>Request a Quote</h1>
    <p>Tell us about your project and our team will reach out with a tailored consultation.</p>
  </div>

  <div class="contact-page__grid">
    <aside class="contact-page__info" aria-label="Contact information">
      <h2>Contact Information</h2>
      <p class="contact-page__info-lead">Reach us directly or fill out the form — we typically respond within one business day.</p>

      <ul class="contact-page__details">
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></span>
          <div>
            <span class="contact-page__detail-label">Phone</span>
            <a href="tel:<?php echo e($site['phone_tel']); ?>"><?php echo e($site['phone']); ?></a>
          </div>
        </li>
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
          <div>
            <span class="contact-page__detail-label">Email</span>
            <a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a>
          </div>
        </li>
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
          <div>
            <span class="contact-page__detail-label">Office</span>
            <span><?php echo $site['address']; ?></span>
          </div>
        </li>
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-clock"></i></span>
          <div>
            <span class="contact-page__detail-label">Business Hours</span>
            <span>Mon – Sat, 9:00 AM – 6:00 PM IST</span>
          </div>
        </li>
      </ul>

      <div class="contact-page__social">
        <span class="contact-page__detail-label">Follow Us</span>
        <div class="contact-page__social-links">
          <?php foreach ($site['social'] as $s): ?>
            <a href="<?php echo e($s['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo e($s['label']); ?>">
              <i class="fa-brands <?php echo e($s['icon']); ?>" aria-hidden="true"></i>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>

    <div class="contact-page__form-wrap">
      <?php echo flash(); ?>

      <?php if ($errors): ?>
        <div class="contact-alert contact-alert--error" role="alert">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="contact-form" id="contactForm" method="POST" action="/contact" novalidate>
        <?php echo CSRF::field(); ?>

        <div class="contact-form__field">
          <label for="contactFullName">Full Name <span aria-hidden="true">*</span></label>
          <input type="text" id="contactFullName" name="full_name" required autocomplete="name"
                 minlength="2" maxlength="120" placeholder="Your full name"
                 value="<?php echo e($old['full_name']); ?>"
                 aria-describedby="contactFullNameError">
          <p class="contact-form__error" id="contactFullNameError" role="alert"></p>
        </div>

        <div class="contact-form__row">
          <div class="contact-form__field">
            <label for="contactEmail">Email Address <span aria-hidden="true">*</span></label>
            <input type="email" id="contactEmail" name="email" required autocomplete="email"
                   placeholder="you@example.com" inputmode="email"
                   value="<?php echo e($old['email']); ?>"
                   aria-describedby="contactEmailError">
            <p class="contact-form__error" id="contactEmailError" role="alert"></p>
          </div>

          <div class="contact-form__field">
            <label for="contactPhone">Phone Number <span aria-hidden="true">*</span></label>
            <input type="tel" id="contactPhone" name="phone" required autocomplete="tel"
                   placeholder="+91 98765 43210" inputmode="tel"
                   value="<?php echo e($old['phone']); ?>"
                   aria-describedby="contactPhoneError">
            <p class="contact-form__error" id="contactPhoneError" role="alert"></p>
          </div>
        </div>

        <div class="contact-form__field">
          <label for="contactService">Service Type</label>
          <select id="contactService" name="service_type" aria-describedby="contactServiceError">
            <option value="">Select a service (optional)</option>
            <?php foreach ($serviceTypes as $value => $label): ?>
              <option value="<?php echo e($value); ?>"<?php echo $old['service_type'] === $value ? ' selected' : ''; ?>>
                <?php echo e($label); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <p class="contact-form__error" id="contactServiceError" role="alert"></p>
        </div>

        <div class="contact-form__field">
          <label for="contactMessage">Project Details <span aria-hidden="true">*</span></label>
          <textarea id="contactMessage" name="message" required rows="5" minlength="20" maxlength="5000"
                    placeholder="Tell us about your project — location, timeline, budget range, and any specific requirements."
                    aria-describedby="contactMessageError contactMessageCount"><?php echo e($old['message']); ?></textarea>
          <div class="contact-form__meta">
            <p class="contact-form__error" id="contactMessageError" role="alert"></p>
            <span class="contact-form__count" id="contactMessageCount" aria-live="polite">0 / 5000</span>
          </div>
        </div>

        <button type="submit" class="contact-form__submit">Send Message</button>
        <p class="contact-form__note"><span aria-hidden="true">*</span> Required fields</p>
      </form>
    </div>
  </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
