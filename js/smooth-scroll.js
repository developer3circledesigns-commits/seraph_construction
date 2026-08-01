/* =====================================================
   SERAPH BUILD CONSTRUCTION — Smooth Scroll
   Lenis (smooth scroll) synced 1:1 with ScrollTrigger so
   pinned/scrubbed sections (kitchen, materials, projects)
   stay lock-step with the eased page scroll.
   ===================================================== */

(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduceMotion || typeof window.Lenis === 'undefined' ||
      typeof window.ScrollTrigger === 'undefined') {
    return;
  }

  var lenis = new Lenis({
    duration: 1.15,
    easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
    smoothWheel: true,
    wheelMultiplier: 1,
    touchMultiplier: 1.6,
    syncTouch: false,
  });

  /* Feed every Lenis scroll into ScrollTrigger and drive Lenis
     from GSAP's ticker so both share the same frame clock. */
  lenis.on('scroll', ScrollTrigger.update);

  gsap.ticker.add(function (time) {
    lenis.raf(time * 1000);
  });
  gsap.ticker.lagSmoothing(0);

  window.lenis = lenis;

  /* Intercept in-page anchor links so they glide with Lenis
     instead of jumping. data-filter-target links are handled
     by main.js (they need to apply the filter first). */
  document.addEventListener('click', function (e) {
    var link = e.target.closest ? e.target.closest('a[href^="#"]') : null;
    if (!link || link.hasAttribute('data-filter-target')) {
      return;
    }
    var id = link.getAttribute('href');
    if (!id || id.length < 2) {
      return;
    }
    var target = document.getElementById(id.slice(1));
    if (!target) {
      return;
    }
    e.preventDefault();
    lenis.scrollTo(target, { offset: 0, duration: 1.3 });
  });
})();
