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

$prefillService = trim((string)($_GET['service'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $prefillService !== '' && array_key_exists($prefillService, $serviceTypes)) {
    $old['service_type'] = $prefillService;
}

if (isset($_GET['clear_estimate'])) {
    unset($_SESSION['calc_estimate']);
    redirect('/contact');
}

/* Step 1: the cost calculator on packages.php posts its figures here.
   Validate, recompute the total server-side, park it in the session and
   bounce the visitor to the enquiry form. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calc_estimate'])) {
    $body = request_body();

    if (!CSRF::verify($body['_csrf'] ?? null)) {
        redirect('/packages', 'Your session has expired. Please try the calculator again.', 'error');
    }

    $floors = max(1, min(6, (int)($body['calc_floors'] ?? 1)));
    $packageKey = (($body['calc_package'] ?? '') === 'elite') ? 'elite' : 'premium';
    $rate = $packageKey === 'elite' ? 2900 : 2300;

    $floorNames = ['Ground Floor', 'First Floor', 'Second Floor', 'Third Floor', 'Fourth Floor', 'Fifth Floor'];
    $areas = [];
    $total = 0;
    for ($i = 1; $i <= $floors; $i++) {
        $area = max(0, (float)($body['calc_area_' . $i] ?? 0));
        $areas[] = ['label' => $floorNames[$i - 1], 'area' => $area];
        $total += (int)round($area * $rate);
    }

    $sump = max(0, (float)($body['calc_sump'] ?? 0));
    $septic = max(0, (float)($body['calc_septic'] ?? 0));
    $wallL = max(0, (float)($body['calc_wall_l'] ?? 0));
    $wallH = max(0, (float)($body['calc_wall_h'] ?? 0));

    $sumpCost = (int)round($sump * 30);
    $septicCost = (int)round($septic * 30);
    $wallCost = (int)round($wallL * $wallH * 425);
    $total += $sumpCost + $septicCost + $wallCost;

    $_SESSION['calc_estimate'] = [
        'package'  => $packageKey,
        'rate'     => $rate,
        'floors'   => $areas,
        'sump_ltr' => $sump,
        'septic_ltr' => $septic,
        'wall_l'   => $wallL,
        'wall_h'   => $wallH,
        'total'    => $total,
    ];

    release_session_lock();
    redirect('/contact', 'Your estimate is ready — fill in your details below and we will send you a detailed quote.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = request_body();
    $old = array_merge($old, array_intersect_key($body, $old));

    if (!CSRF::verify($body['_csrf'] ?? null)) {
        $errors[] = 'Your session has expired. Please refresh the page and try again.';
    }

    // Release the session lock before DB/mail work so other tabs can load /contact.
    release_session_lock();

    $fullName = trim((string)($body['full_name'] ?? ''));
    $email = trim((string)($body['email'] ?? ''));
    $phone = trim((string)($body['phone'] ?? ''));
    $serviceType = trim((string)($body['service_type'] ?? ''));
    $message = trim((string)($body['message'] ?? ''));

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    } elseif (strlen($fullName) < 2 || strlen($fullName) > 120) {
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

    /* Project details are optional — any length including empty. */

    if (!$errors) {
        try {
                $ip = client_ip();
            $recentCount = ContactInquiry::recentCountByIp($ip);

            if ($recentCount >= 5) {
                $errors[] = 'Too many requests. Please wait an hour before submitting again.';
            } else {
                $calc = $_SESSION['calc_estimate'] ?? null;
                $inquiryId = ContactInquiry::create([
                    'full_name'    => $fullName,
                    'email'        => $email,
                    'phone'        => $phone,
                    'service_type' => $serviceType,
                    'message'      => $message,
                    'ip_address'   => $ip,
                    'calc'         => is_array($calc) ? $calc : null,
                ]);
                if (function_exists('session_reopen_if_needed')) {
                    session_reopen_if_needed();
                }
                unset($_SESSION['calc_estimate']);

                try {
                    Notification::notifyAllAdmins(
                        'contact_inquiry',
                        'New contact enquiry',
                        $fullName . ' (' . $email . ')' . (is_array($calc) ? ' — est. ₹' . inr_format($calc['total']) : ''),
                        $inquiryId
                    );
                } catch (Throwable $notifyErr) {
                    error_log('Contact inquiry admin notification failed: ' . $notifyErr->getMessage());
                }

                $serviceLabel = ContactInquiry::serviceLabel($serviceType !== '' ? $serviceType : null);
                $adminHost = (string)($_SERVER['HTTP_HOST'] ?? '');
                $adminScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $adminLink = $adminHost !== ''
                    ? $adminScheme . '://' . $adminHost . '/admin/contact-inquiry-view?id=' . $inquiryId
                    : '/admin/contact-inquiry-view?id=' . $inquiryId;

                $mailBody = "New contact enquiry #{$inquiryId}\n\n"
                    . "Name: {$fullName}\n"
                    . "Email: {$email}\n"
                    . "Phone: {$phone}\n"
                    . "Service: {$serviceLabel}\n\n"
                    . "Message:\n" . ($message !== '' ? $message : '(Not provided)') . "\n\n";

                if (is_array($calc)) {
                    $mailBody .= "Cost Calculator Estimate:\n";
                    $mailBody .= 'Package: ' . ucfirst((string)$calc['package']) . ' @ ₹' . inr_format($calc['rate']) . "/sqft\n";
                    foreach ((array)($calc['floors'] ?? []) as $f) {
                        $mailBody .= $f['label'] . ': ' . $f['area'] . " sqft\n";
                    }
                    $mailBody .= 'Water sump: ' . $calc['sump_ltr'] . " ltr\n";
                    $mailBody .= 'Septic tank: ' . $calc['septic_ltr'] . " ltr\n";
                    $mailBody .= 'Compound wall: ' . $calc['wall_l'] . ' x ' . $calc['wall_h'] . " sqft\n";
                    $mailBody .= 'Estimated total: ₹' . inr_format($calc['total']) . "\n\n";
                }

                $mailBody .= "View in admin: {$adminLink}\n";

                $mailSubject = 'New contact enquiry from ' . $fullName;
                $mailReplyTo = $email;

                register_shutdown_function(static function () use ($mailSubject, $mailBody, $mailReplyTo, $inquiryId): void {
                    if (function_exists('fastcgi_finish_request')) {
                        @fastcgi_finish_request();
                    }
                    if (!Mail::send($mailSubject, $mailBody, $mailReplyTo)) {
                        error_log('Contact enquiry #' . $inquiryId . ' saved; email notification not sent (MAIL_TO not configured or mail() failed).');
                    }
                });

                redirect('/contact', 'Thank you! Your message has been sent. Our team will contact you shortly.');
            }
        } catch (Throwable $e) {
            error_log('Contact form failed: ' . $e->getMessage());
            $errors[] = 'Unable to send your message right now. Please call us directly or try again later.';
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
          <div class="contact-page__detail-body">
            <span class="contact-page__detail-label">Phone</span>
            <a class="contact-page__detail-value" href="tel:<?php echo e($site['phone_tel']); ?>"><?php echo e($site['phone']); ?></a>
          </div>
        </li>
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
          <div class="contact-page__detail-body">
            <span class="contact-page__detail-label">Email</span>
            <a class="contact-page__detail-value" href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a>
          </div>
        </li>
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
          <div class="contact-page__detail-body">
            <span class="contact-page__detail-label">Office</span>
            <span class="contact-page__detail-value"><?php echo $site['address']; ?></span>
          </div>
        </li>
        <li>
          <span class="contact-page__detail-icon" aria-hidden="true"><i class="fa-solid fa-clock"></i></span>
          <div class="contact-page__detail-body">
            <span class="contact-page__detail-label">Business Hours</span>
            <span class="contact-page__detail-value">Mon – Sat, 9:00 AM – 6:00 PM IST</span>
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

      <form class="contact-form" id="contactForm" method="POST" action="" novalidate>
        <?php echo CSRF::field(); ?>

        <?php $calcSession = $_SESSION['calc_estimate'] ?? null; ?>
        <?php if (is_array($calcSession)): ?>
          <div class="contact-alert" role="note" style="background:rgba(231,201,89,.12);border:1px solid rgba(231,201,89,.4);padding:1rem;border-radius:8px;margin-bottom:1rem">
            <p style="margin:0 0 .5rem"><strong>Your cost estimate</strong> — <?php echo e(ucfirst((string)$calcSession['package'])); ?> package @ ₹<?php echo inr_format($calcSession['rate']); ?>/sqft, total <strong>₹<?php echo inr_format($calcSession['total']); ?></strong></p>
            <ul style="margin:0 0 .5rem;padding-left:1.25rem">
              <?php foreach ((array)$calcSession['floors'] as $f): ?>
                <li><?php echo e($f['label']); ?>: <?php echo e((string)$f['area']); ?> sqft</li>
              <?php endforeach; ?>
              <li>Water sump: <?php echo e((string)$calcSession['sump_ltr']); ?> ltr &middot; Septic tank: <?php echo e((string)$calcSession['septic_ltr']); ?> ltr &middot; Compound wall: <?php echo e((string)$calcSession['wall_l']); ?> × <?php echo e((string)$calcSession['wall_h']); ?> sqft</li>
            </ul>
            <a href="/contact?clear_estimate=1" style="font-size:.85rem">Remove this estimate</a>
          </div>
        <?php endif; ?>

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
          <label for="contactMessage">Project Details <span class="contact-form__optional">(optional)</span></label>
          <textarea id="contactMessage" name="message" rows="5"
                    placeholder="Tell us about your project — location, timeline, budget range, and any specific requirements."
                    aria-describedby="contactMessageError contactMessageCount"><?php echo e($old['message']); ?></textarea>
          <div class="contact-form__meta">
            <p class="contact-form__error" id="contactMessageError" role="alert"></p>
            <span class="contact-form__count" id="contactMessageCount" aria-live="polite">0 characters</span>
          </div>
        </div>

        <button type="submit" class="contact-form__submit">Send Message</button>
        <p class="contact-form__note"><span aria-hidden="true">*</span> Required fields</p>
      </form>
    </div>
  </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
