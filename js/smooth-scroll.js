/* =====================================================
   SERAPH BUILD CONSTRUCTION — Smooth Scroll
   Lenis (smooth scroll) synced 1:1 with ScrollTrigger so
   pinned/scrubbed sections (kitchen, materials, projects)
   stay lock-step with the eased page scroll.

   Also handles all in-page navigation:
   - glides anchor clicks with Lenis
   - offsets for the fixed top bar so targets aren't hidden
   - resolves pinned-section targets to their true start
   - supports deep links (URL #hash) on load
   - data-filter-target handled by main.js
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

  /* Height of the fixed top bar so section tops are never hidden
     underneath it after navigating. Measured live so it adapts. */
  var topbarOffset = function () {
    var tb = document.querySelector('.topbar');
    return tb ? tb.offsetHeight : 0;
  };

  /* Find the ScrollTrigger instance that pins the given element (or
     one of its ancestors) and return its starting scroll position.
     Pinned sections report a live rect of 0 while pinned, so we need
     this to know where they truly begin. Returns NaN if none found. */
  var pinnedStart = function (target) {
    if (!window.ScrollTrigger) { return NaN; }
    var triggers = window.ScrollTrigger.getAll();
    for (var i = 0; i < triggers.length; i++) {
      var t = triggers[i];
      if (!t.vars || !t.vars.pin) { continue; }
      var triggerEl = (t.trigger && t.trigger.nodeType === 1) ? t.trigger : null;
      if (triggerEl === target || (triggerEl && triggerEl.contains(target)) ||
          (target.id && triggerEl && triggerEl.id === target.id)) {
        return t.start; // numeric scroll offset (pins set start:'top top')
      }
    }
    return NaN;
  };

  /* Compute the correct destination scroll position for an element. */
  var resolveScroll = function (target) {
    var start = pinnedStart(target);
    if (!isNaN(start)) { return start; }
    var rect = target.getBoundingClientRect();
    return rect.top + (window.lenis ? window.lenis.scroll : window.scrollY);
  };

  var goToHash = function (hash) {
    if (!hash || hash.length < 2) { return; }
    var target = document.getElementById(hash.slice(1));
    if (!target) { return; }
    lenis.scrollTo(resolveScroll(target), {
      offset: -topbarOffset(),
      duration: 1.3,
      onComplete: function () { window.ScrollTrigger && window.ScrollTrigger.update(); },
    });
  };

  /* Glide in-page anchor clicks with Lenis. data-filter-target links
     are handled by main.js (they apply the filter first). */
  document.addEventListener('click', function (e) {
    var link = e.target.closest ? e.target.closest('a[href^="#"]') : null;
    if (!link || link.hasAttribute('data-filter-target') || link.hasAttribute('data-placeholder')) {
      return;
    }
    var id = link.getAttribute('href');
    if (!id || id.length < 2) { return; }
    var target = document.getElementById(id.slice(1));
    if (!target) { return; }
    e.preventDefault();
    lenis.scrollTo(resolveScroll(target), { offset: -topbarOffset(), duration: 1.3 });
  });

  /* Deep-link support: if the URL carries a #section hash (shared link,
     redirect, or reload), scroll to it after pinned spacers are ready. */
  var deepHash = window.location.hash;
  if (deepHash) {
    var runDeepLink = function () {
      if (window.ScrollTrigger) { window.ScrollTrigger.refresh(); }
      goToHash(deepHash);
    };
    if (document.readyState === 'complete') {
      requestAnimationFrame(runDeepLink);
    } else {
      window.addEventListener('load', runDeepLink);
    }
  }
})();