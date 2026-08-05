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