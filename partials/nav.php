<?php
/**
 * Navigation partial — topbar, mobile menu and side nav.
 * Expects $site array to be available.
 */
?>
  <!-- Topbar (Layout 13 style) -->
  <header class="topbar">
    <a href="#hero" class="brand"><img src="images/seraph-logo@204w.webp" srcset="images/seraph-logo@204w.webp 204w, images/seraph-logo@102w.webp 102w" sizes="204px" alt="SERAPH BUILD CONSTRUCTION" width="400" height="94"></a>
    <nav class="topbar__nav" aria-label="Primary navigation">
      <?php foreach ($site['nav'] as $href => $label): ?>
        <a href="#<?php echo htmlspecialchars($href); ?>"><?php echo htmlspecialchars($label); ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="topbar__actions">
      <button class="topbar__login" id="loginToggle" data-login-open aria-haspopup="dialog" aria-controls="loginModal">
        <i class="fa-solid fa-user" aria-hidden="true"></i><span>Sign In</span>
      </button>
      <a href="#contact" class="btn btn--solid topbar__quote">Get a Quote</a>
      <button class="topbar__menu" id="menuToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
        <span></span><span></span>
      </button>
    </div>
  </header>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <nav aria-label="Mobile navigation">
      <?php foreach ($site['nav'] as $href => $label): ?>
        <a href="#<?php echo htmlspecialchars($href); ?>" class="mobile-menu__link"><?php echo htmlspecialchars($label); ?></a>
      <?php endforeach; ?>
      <button class="btn mobile-menu__login" data-login-open aria-haspopup="dialog" aria-controls="loginModal">
        <i class="fa-solid fa-user" aria-hidden="true"></i> Sign In
      </button>
      <a href="#contact" class="btn btn--solid mobile-menu__quote">Get a Quote</a>
    </nav>
  </div>

  <!-- Side Nav — fixed left rail with section progress -->
  <nav class="side-nav" id="sideNav" aria-label="Section navigation">
    <span class="side-nav__label">Scroll</span>
    <ul class="side-nav__list">
      <?php foreach ($site['nav'] as $href => $label): ?>
        <li><a href="#<?php echo htmlspecialchars($href); ?>" class="side-nav__link" data-side-nav><span class="side-nav__dot"></span><span class="side-nav__name"><?php echo htmlspecialchars($label); ?></span></a></li>
      <?php endforeach; ?>
      <li><a href="#contact" class="side-nav__link" data-side-nav><span class="side-nav__dot"></span><span class="side-nav__name">Contact</span></a></li>
    </ul>
    <span class="side-nav__progress" id="sideNavProgress"></span>
  </nav>

  <!-- Sign In modal — Client / Admin -->
  <div class="login-modal" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle" aria-hidden="true">
    <div class="login-modal__backdrop" data-login-close></div>
    <div class="login-modal__panel">
      <button type="button" class="login-modal__close" data-login-close aria-label="Close sign in">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
      </button>

      <div class="login-modal__head">
        <span class="login-modal__eyebrow">Seraph Build Construction</span>
        <h2 id="loginModalTitle">Welcome back</h2>
        <p>Choose your account type to continue.</p>
      </div>

      <div class="login-modal__tabs" role="tablist" aria-label="Account type">
        <button type="button" class="login-modal__tab is-active" id="loginTabClient" role="tab" aria-selected="true" aria-controls="loginPaneClient" data-login-tab="client">
          <i class="fa-solid fa-house" aria-hidden="true"></i><span>Client</span>
        </button>
        <button type="button" class="login-modal__tab" id="loginTabAdmin" role="tab" aria-selected="false" aria-controls="loginPaneAdmin" data-login-tab="admin">
          <i class="fa-solid fa-shield-halved" aria-hidden="true"></i><span>Admin</span>
        </button>
      </div>

      <div class="login-modal__panes">
        <section class="login-modal__pane is-active" id="loginPaneClient" role="tabpanel" aria-labelledby="loginTabClient" data-login-pane="client">
          <p class="login-modal__hint"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Track your project's live progress and daily updates.</p>
          <form class="login-form" method="POST" action="/client/login" novalidate>
            <?php echo CSRF::field(); ?>
            <div class="login-form__field">
              <label for="loginClientEmail">Email address</label>
              <input type="email" id="loginClientEmail" name="email" required autocomplete="username" placeholder="you@example.com">
            </div>
            <div class="login-form__field">
              <label for="loginClientPassword">Password</label>
              <input type="password" id="loginClientPassword" name="password" required autocomplete="current-password" placeholder="Enter your password">
            </div>
            <button type="submit" class="login-form__submit">Sign In</button>
          </form>
        </section>

        <section class="login-modal__pane" id="loginPaneAdmin" role="tabpanel" aria-labelledby="loginTabAdmin" data-login-pane="admin">
          <p class="login-modal__hint"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Manage projects, clients and daily construction updates.</p>
          <form class="login-form" method="POST" action="/admin/login" novalidate>
            <?php echo CSRF::field(); ?>
            <div class="login-form__field">
              <label for="loginAdminEmail">Email address</label>
              <input type="email" id="loginAdminEmail" name="email" required autocomplete="username" placeholder="admin@seraphbuild.com">
            </div>
            <div class="login-form__field">
              <label for="loginAdminPassword">Password</label>
              <input type="password" id="loginAdminPassword" name="password" required autocomplete="current-password" placeholder="Enter your password">
            </div>
            <button type="submit" class="login-form__submit">Sign In</button>
          </form>
        </section>
      </div>

      <p class="login-modal__foot">Need an account? <a href="#contact" data-login-close>Contact our team</a></p>
    </div>
  </div>