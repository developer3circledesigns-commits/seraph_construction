<?php
/**
 * Footer + closing scripts.
 * Expects $site array to be available.
 */
$currentPage = basename((string)($_SERVER['SCRIPT_NAME'] ?? 'index.php'), '.php');
$homePrefix  = ($currentPage === 'index' || $currentPage === '') ? '' : 'index.php';
$contactUrl  = $site['contact_url'] ?? 'contact.php';
?>
  <!-- 7. FOOTER — Footer 02 marquee band + original footer content -->
  <footer class="site-footer footer-02">
    <div class="marquee" aria-hidden="true">
      <div class="marquee__track" id="marqueeTrack">
        <span class="marquee__item">SERAPH <em>&bull;</em> BUILD <em>&bull;</em></span>
        <span class="marquee__item">SERAPH <em>&bull;</em> BUILD <em>&bull;</em></span>
        <span class="marquee__item">SERAPH <em>&bull;</em> BUILD <em>&bull;</em></span>
      </div>
    </div>

    <div class="container footer-grid">
      <div class="footer-brand">
        <a href="<?php echo $homePrefix; ?>#hero" class="brand"><img src="images/footer-logo@200w.webp" srcset="images/footer-logo@200w.webp 200w, images/Footer_Logo.webp 400w" sizes="200px" alt="SERAPH BUILD CONSTRUCTION" width="200" height="114"></a>
        <p>Crafting extraordinary spaces where architecture meets artistry. Premium construction and design for those who demand excellence.</p>
        <div class="footer-social">
          <?php foreach ($site['social'] as $s): ?>
            <a href="<?php echo $s['url']; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($s['label']); ?>"><i class="fa-brands <?php echo $s['icon']; ?>" aria-hidden="true"></i></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="footer-col">
        <h3 class="footer-heading">Quick Links</h3>
        <ul class="footer-links">
          <?php foreach ($site['nav'] as $href => $label): ?>
            <li><a href="<?php echo $href === 'projects' ? 'projects.php' : $homePrefix . '#' . htmlspecialchars($href); ?>"><?php echo htmlspecialchars($label); ?></a></li>
          <?php endforeach; ?>
          <li><a href="<?php echo $contactUrl; ?>">Contact</a></li>
          <li><a href="<?php echo $homePrefix; ?>#materials">Materials</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h3 class="footer-heading">Our Services</h3>
        <ul class="footer-links">
          <li><a href="<?php echo $homePrefix; ?>#services">Construction</a></li>
          <li><a href="<?php echo $homePrefix; ?>#services">Interior Design</a></li>
          <li><a href="<?php echo $homePrefix; ?>#services">Modular Kitchen</a></li>
          <li><a href="<?php echo $homePrefix; ?>#materials">Materials</a></li>
          <li><a href="<?php echo $homePrefix; ?>#services">Renovation</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h3 class="footer-heading">Contact Us</h3>
        <ul class="footer-contact">
          <li>
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
            <a href="tel:<?php echo $site['phone_tel']; ?>"><?php echo $site['phone']; ?></a>
          </li>
          <li>
            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
            <a href="mailto:<?php echo $site['email']; ?>"><?php echo $site['email']; ?></a>
          </li>
          <li>
            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
            <span><?php echo $site['address']; ?></span>
          </li>
        </ul>

        <h3 class="footer-heading mt-4">Newsletter</h3>
        <form class="newsletter-form" id="newsletterForm" aria-label="Newsletter signup" novalidate>
          <label for="newsletterEmail" class="visually-hidden">Email address</label>
          <input type="email" id="newsletterEmail" name="email" placeholder="Your email address" required autocomplete="email" aria-describedby="newsletterStatus" inputmode="email">
          <button type="submit" aria-label="Subscribe to newsletter">
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
          </button>
        </form>
        <p id="newsletterStatus" class="newsletter-status" role="status" aria-live="polite"></p>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom__inner">
        <p>&copy; <span id="year"></span> SERAPH BUILD CONSTRUCTION. All rights reserved.</p>
        <div class="footer-legal">
          <a href="privacy.php">Privacy Policy</a>
          <a href="terms.php">Terms &amp; Conditions</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts — Layout 13 uses GSAP + ScrollTrigger + Lenis (smooth scroll) -->
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
  <script src="js/vendor/lenis.min.js" defer></script>
  <script src="js/smooth-scroll.js" defer></script>
  <script src="js/responsive-images.js" defer></script>
  <script src="js/animations.js" defer></script>
  <script src="js/main.js" defer></script>
</body>
</html>