<!-- =====================================================
     1. HERO — Layout 13 full-bleed blur panel
     ===================================================== -->
<?php $projectsUrl = $site['projects_url'] ?? 'projects.php'; ?>
<section id="hero" class="blur-panel" data-blur>
  <div class="blur-panel__media">
    <img
      src="images/hero-seraph@1280w.webp"
      srcset="images/hero-seraph@640w.webp 640w, images/hero-seraph@960w.webp 960w, images/hero-seraph@1280w.webp 1280w, images/hero-seraph@1672w.webp 1672w"
      sizes="100vw"
      alt="Luxury modern villa exterior at dusk with architectural lighting"
      width="1672"
      height="942"
      fetchpriority="high"
      decoding="async"
    >
  </div>
  <div class="blur-panel__content hero__content">
    <span class="eyebrow">Luxury Construction &middot; Since <?php echo htmlspecialchars($site['since']); ?></span>
    <h1 class="hero__title">
      <span class="line"><span class="line-inner">Building Premium Spaces.</span></span>
      <span class="line"><span class="line-inner">Creating Timeless Experiences</span></span>
    </h1>
    <p>Construction &middot; Interior Design &middot; Commercial</p>
    <a href="<?php echo e($projectsUrl); ?>" class="btn btn--solid">Explore Our Work</a>
  </div>
  <div class="hero__scroll" aria-hidden="true">
    <div class="mouse"><span class="mouse__wheel"></span></div>
    <span>Scroll to Explore</span>
  </div>
</section>