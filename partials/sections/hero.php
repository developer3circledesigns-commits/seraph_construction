<!-- =====================================================
     1. HERO — Layout 13 full-bleed blur panel
     ===================================================== -->
<section id="hero" class="blur-panel" data-blur>
  <div class="blur-panel__media">
    <img
      src="images/hero-front@1120w.webp"
      srcset="images/hero-front@1120w.webp 1120w, images/hero-front@2240w.webp 2240w"
      sizes="(min-width: 1400px) 1376px, 100vw"
      alt="Luxury modern villa exterior at dusk with architectural lighting"
      width="1376"
      height="768"
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
    <a href="#interior" class="btn btn--solid">Explore Our Work</a>
  </div>
  <div class="hero__scroll" aria-hidden="true">
    <div class="mouse"><span class="mouse__wheel"></span></div>
    <span>Scroll to Explore</span>
  </div>
</section>