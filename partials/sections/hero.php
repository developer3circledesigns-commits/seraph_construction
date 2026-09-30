<!-- =====================================================
     1. HERO — Layout 13 full-bleed blur panel
     ===================================================== -->
<?php $projectsUrl = $site['projects_url'] ?? 'projects.php'; ?>
<section id="hero" class="blur-panel" data-blur>
  <div class="blur-panel__media">
    <img
      src="images/Hero%20Banner_seraph.png"
      alt="Seraph Build Construction — luxury construction, interior design and commercial projects"
      width="1672"
      height="941"
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