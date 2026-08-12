/* =====================================================
   SERAPH BUILD CONSTRUCTION — Smooth Scroll
   Lenis synced with ScrollTrigger after pinned sections
   are measured. Lenis stays paused until animations init
   + layout refresh so pin spacers don't fight the scroller.

   Also handles in-page navigation:
   - glides anchor clicks with Lenis
   - offsets for the fixed top bar
   - resolves pinned-section targets to their true start
   - supports deep links (#hash) on load
   ===================================================== */

(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduceMotion || typeof window.Lenis === 'undefined' ||
      typeof window.ScrollTrigger === 'undefined' ||
      typeof window.gsap === 'undefined') {
    return;
  }

  ScrollTrigger.config({
    ignoreMobileResize: true,
    limitCallbacks: true,
  });

  var lenis = new Lenis({
    /* Lerp tracks scrubbed ScrollTrigger timelines more closely than
       a fixed duration — reduces the "stuck then catch-up" feeling. */
    lerp: 0.09,
    smoothWheel: true,
    wheelMultiplier: 0.85,
    touchMultiplier: 1.2,
    syncTouch: false,
  });

  var scrollReady = false;

  lenis.on('scroll', ScrollTrigger.update);

  gsap.ticker.add(function (time) {
    lenis.raf(time * 1000);
  });
  gsap.ticker.lagSmoothing(0);

  window.lenis = lenis;

  /* Hold smooth scroll until GSAP pins/spacers are calculated. */
  lenis.stop();

  var activateScroll = function () {
    if (scrollReady) { return; }
    scrollReady = true;
    ScrollTrigger.refresh(true);
    lenis.start();
    window.dispatchEvent(new CustomEvent('seraph:scroll-ready'));
  };

  window.addEventListener('seraph:animations-ready', activateScroll, { once: true });

  /* Fallback if animations bundle fails or is skipped on a page. */
  window.addEventListener('load', function () {
    requestAnimationFrame(function () {
      if (!scrollReady) {
        activateScroll();
      }
    });
  }, { once: true });

  var topbarOffset = function () {
    var tb = document.querySelector('.topbar');
    return tb ? tb.offsetHeight : 0;
  };

  var pinnedStart = function (target) {
    if (!window.ScrollTrigger) { return NaN; }
    var triggers = ScrollTrigger.getAll();
    for (var i = 0; i < triggers.length; i++) {
      var t = triggers[i];
      if (!t.vars || !t.vars.pin) { continue; }
      var triggerEl = (t.trigger && t.trigger.nodeType === 1) ? t.trigger : null;
      if (triggerEl === target || (triggerEl && triggerEl.contains(target)) ||
          (target.id && triggerEl && triggerEl.id === target.id)) {
        return t.start;
      }
    }
    return NaN;
  };

  var resolveScroll = function (target) {
    var start = pinnedStart(target);
    if (!isNaN(start)) { return start; }
    var rect = target.getBoundingClientRect();
    return rect.top + lenis.scroll;
  };

  var goToHash = function (hash) {
    if (!hash || hash.length < 2) { return; }
    var target = document.getElementById(hash.slice(1));
    if (!target) { return; }
    lenis.scrollTo(resolveScroll(target), {
      offset: -topbarOffset(),
      duration: 1.1,
      onComplete: function () { ScrollTrigger.update(); },
    });
  };

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
    lenis.scrollTo(resolveScroll(target), { offset: -topbarOffset(), duration: 1.1 });
  });

  var deepHash = window.location.hash;
  if (deepHash) {
    var runDeepLink = function () {
      ScrollTrigger.refresh();
      goToHash(deepHash);
    };
    window.addEventListener('seraph:scroll-ready', runDeepLink, { once: true });
  }
})();
