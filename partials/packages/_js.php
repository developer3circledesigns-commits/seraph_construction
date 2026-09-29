<?php
/**
 * Shared behaviour for the SBC Packages page.
 *
 * One dependency-free script provides the accessible primitives the page
 * relies on — accordions, tabs, the contents rail, the specification
 * filter and scroll reveals — so the page markup never has to hand-roll
 * ARIA wiring and the behaviour survives a change to the data set.
 *
 * Every module below no-ops when its markup is absent, so the page can
 * drop a component without touching this file.
 *
 * Markup contract (all optional):
 *
 *   Accordion   [data-sk-acc]  >  button[data-sk-acc-btn] + [data-sk-acc-panel]
 *   Tabs        [data-sk-tabs] >  button[data-sk-tab] + [data-sk-tabpanel]
 *   Rail        [data-sk-rail] >  a[data-sk-rail-link][href="#id"] against [data-sk-rail-target]
 *   Filter      input[data-sk-filter], input[data-sk-filter-diff],
 *               button[data-sk-group-chip][data-group], button[data-sk-filter-reset],
 *               [data-sk-filter-scope] > [data-sk-group] > [data-sk-item][data-sk-text]
 *   Reveal      [data-sk-reveal]
 */

declare(strict_types=1);
?>
<script>
(function () {
  'use strict';

  var $  = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------- *
   * Header offset
   *
   * Everything sticky on the page is offset by --sk-head so it
   * parks directly under the fixed site header. .topbar's height is
   * not a constant: it is whichever child is tallest (logo, 44px
   * hamburger, quote button) and that changes at four breakpoints in
   * css/responsive.css. So measure it rather than hard-coding it.
   * ---------------------------------------------------------- */
  (function headerOffset() {
    var bar = $('.topbar');
    var targets = $$('.sk-scope, .sk');
    if (!bar || !targets.length) { return; }

    function sync() {
      var h = Math.round(bar.getBoundingClientRect().height);
      if (!h) { return; }
      targets.forEach(function (el) { el.style.setProperty('--sk-head', h + 'px'); });
    }

    sync();
    window.addEventListener('resize', sync);
    window.addEventListener('load', sync);
    // A late-loading webfont can change the header height after load.
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(sync); }
  })();

  /* ---------------------------------------------------------- *
   * Sticky toolbar height
   *
   * Anything anchored *inside* the specification has to clear the
   * sticky toolbar, not just the site header. The toolbar wraps
   * whenever its search field reflows, so measure it instead of
   * hard-coding a breakpoint table. --sk-sticky is where the toolbar
   * parks; --sk-toolbar is how much room it occupies below that.
   * ---------------------------------------------------------- */
  (function toolbarOffset() {
    var bar = $('[data-sk-stickybar], .sk-stickybar');
    var targets = $$('.sk-scope, .sk');
    if (!bar || !targets.length) { return; }

    function sync() {
      var h = Math.round(bar.getBoundingClientRect().height);
      if (!h) { return; }
      targets.forEach(function (el) { el.style.setProperty('--sk-toolbar', h + 'px'); });
    }

    sync();
    window.addEventListener('resize', sync);
    window.addEventListener('load', sync);
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(sync); }
  })();

  /* ---------------------------------------------------------- *
   * Anchor offsets for the contents rail
   *
   * The site-wide smooth-scroll handler in js/smooth-scroll.js claims
   * every a[href^="#"] and scrolls through Lenis with a fixed
   * -topbarOffset(). It has no idea a sticky toolbar sits below the
   * header, so it would park the section heading underneath it and
   * scroll-margin-top would be ignored. Stopping that handler in the
   * capture phase — without preventDefault — leaves the browser's own
   * anchor navigation, which does honour scroll-margin-top.
   * ---------------------------------------------------------- */
  (function railAnchors() {
    if (!$('.sk-rail')) { return; }
    document.addEventListener('click', function (e) {
      var link = e.target.closest ? e.target.closest('a[data-sk-rail-link]') : null;
      if (!link) { return; }
      e.stopPropagation();
    }, true);
  })();

  /* ---------------------------------------------------------- *
   * Back to top
   * ---------------------------------------------------------- */
  (function top() {
    var btn = $('[data-sk-top]');
    if (!btn) { return; }
    var ticking = false;
    function sync() {
      btn.classList.toggle('is-on', window.scrollY > 600);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(sync); }
    }, { passive: true });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    sync();
  })();

  /* ---------------------------------------------------------- *
   * Accordion  —  click + Enter/Space, one panel per group
   * ---------------------------------------------------------- */
  (function accordions() {
    $$('[data-sk-acc]').forEach(function (acc) {
      var single = acc.hasAttribute('data-sk-acc-single');
      var buttons = $$('[data-sk-acc-btn]', acc);

      buttons.forEach(function (btn) {
        var panel = document.getElementById(btn.getAttribute('aria-controls'));
        if (!panel) { return; }

        btn.addEventListener('click', function () {
          var open = btn.getAttribute('aria-expanded') === 'true';
          if (single && !open) {
            buttons.forEach(function (other) {
              if (other === btn) { return; }
              var p = document.getElementById(other.getAttribute('aria-controls'));
              other.setAttribute('aria-expanded', 'false');
              if (p) { p.hidden = true; }
            });
          }
          btn.setAttribute('aria-expanded', open ? 'false' : 'true');
          panel.hidden = open;
        });
      });

      if (acc.hasAttribute('data-sk-acc-expand')) {
        buttons.forEach(function (btn) {
          var panel = document.getElementById(btn.getAttribute('aria-controls'));
          btn.setAttribute('aria-expanded', 'true');
          if (panel) { panel.hidden = false; }
        });
      }
    });

    $$('[data-sk-acc-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = document.getElementById(btn.getAttribute('data-sk-acc-toggle'));
        if (!target) { return; }
        var open = btn.getAttribute('aria-expanded') === 'true';
        $$('[data-sk-acc-btn]', target).forEach(function (b) {
          var p = document.getElementById(b.getAttribute('aria-controls'));
          b.setAttribute('aria-expanded', open ? 'false' : 'true');
          if (p) { p.hidden = open; }
        });
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
      });
    });
  })();

  /* ---------------------------------------------------------- *
   * Tabs  —  click, ArrowLeft/Right, Home/End, roving tabindex
   * ---------------------------------------------------------- */
  (function tabs() {
    $$('[data-sk-tabs]').forEach(function (root) {
      var tabs   = $$('[data-sk-tab]', root);
      var panels = $$('[data-sk-tabpanel]', root);
      if (!tabs.length) { return; }

      function select(idx, focus) {
        idx = (idx + tabs.length) % tabs.length;
        tabs.forEach(function (t, i) {
          var on = i === idx;
          t.setAttribute('aria-selected', on ? 'true' : 'false');
          t.tabIndex = on ? 0 : -1;
          var p = document.getElementById(t.getAttribute('aria-controls'));
          if (p) { p.hidden = !on; }
        });
        if (focus) { tabs[idx].focus(); }
        if (history.replaceState) {
          history.replaceState(null, '', '#' + tabs[idx].getAttribute('aria-controls'));
        }
      }

      tabs.forEach(function (t, i) {
        t.addEventListener('click', function () { select(i, false); });
        t.addEventListener('keydown', function (e) {
          var k = e.key;
          if (k === 'ArrowRight' || k === 'ArrowDown') { e.preventDefault(); select(i + 1, true); }
          else if (k === 'ArrowLeft' || k === 'ArrowUp') { e.preventDefault(); select(i - 1, true); }
          else if (k === 'Home') { e.preventDefault(); select(0, true); }
          else if (k === 'End') { e.preventDefault(); select(tabs.length - 1, true); }
        });
      });

      // Deep link: /packages.php#sec-electrical opens that panel.
      var hash = window.location.hash.slice(1);
      var fromHash = hash ? tabs.findIndex(function (t) {
        return t.getAttribute('aria-controls') === hash;
      }) : -1;

      if (fromHash > -1) {
        select(fromHash, false);
      } else if (!tabs.some(function (t) { return t.getAttribute('aria-selected') === 'true'; })) {
        select(0, false);
      }
    });
  })();

  /* ---------------------------------------------------------- *
   * Contents rail scroll-spy
   * ---------------------------------------------------------- */
  (function rail() {
    var links = $$('[data-sk-rail-link]');
    if (!links.length) { return; }

    var targets = links.map(function (a) {
      return document.getElementById((a.getAttribute('href') || '').slice(1));
    }).filter(Boolean);
    if (!targets.length) { return; }

    // Measure against the document, not offsetTop — offsetTop is
    // relative to the nearest positioned ancestor, which is not the
    // scroll origin once a layout wraps its sections in a column.
    function docTop(el) {
      return el.getBoundingClientRect().top + window.scrollY;
    }

    function update() {
      var sticky = parseFloat(getComputedStyle(document.querySelector('.sk')).getPropertyValue('--sk-sticky')) || 120;
      var line = window.scrollY + sticky + 80;
      var active = 0;
      targets.forEach(function (t, i) { if (docTop(t) <= line) { active = i; } });
      links.forEach(function (a, i) { a.classList.toggle('is-active', i === active); });
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(function () { update(); ticking = false; }); }
    }, { passive: true });
    window.addEventListener('resize', update);
    update();
  })();

  /* ---------------------------------------------------------- *
   * Specification filter
   *   text query  +  "differences only"  +  section chips
   * ---------------------------------------------------------- */
  (function filter() {
    var scope = $('[data-sk-filter-scope]');
    if (!scope) { return; }

    var input   = $('[data-sk-filter]');
    var diffBox = $('[data-sk-filter-diff]');
    var resets  = $$('[data-sk-filter-reset]');
    var count   = $('[data-sk-filter-count]');
    var empty   = $('[data-sk-filter-empty]');
    var chips   = $$('[data-sk-group-chip]');
    var items   = $$('[data-sk-item]', scope);
    var groups  = $$('[data-sk-group]', scope);
    var total   = items.length;
    var word    = count ? (count.getAttribute('data-sk-count-word') || 'items') : 'items';

    var state = { q: '', diff: false, group: '' };

    function apply() {
      var shown = 0;
      var groupsSeen = {};

      items.forEach(function (item) {
        var text = item.getAttribute('data-sk-text') || '';
        var same = item.getAttribute('data-sk-flag') === 'same';
        var group = item.getAttribute('data-sk-group-key') || '';

        var okQ = state.q === '' || text.indexOf(state.q) !== -1;
        var okD = !state.diff || !same;
        var okG = state.group === '' || group === state.group;

        var visible = okQ && okD && okG;
        item.hidden = !visible;
        if (visible) { shown++; groupsSeen[group] = true; }
      });

      groups.forEach(function (g) {
        var key = g.getAttribute('data-sk-group') || '';
        g.hidden = key !== '' && !groupsSeen[key];
      });

      if (count) {
        var filtered = state.q !== '' || state.diff || state.group !== '';
        // A layout can voice the count its own way — the console layout
        // wants "36/59 rows", the document layouts want a sentence.
        // {n} is the rows on screen, {total} is every row in the scope.
        var fmt = function (attr) {
          return count.getAttribute(attr)
            || (filtered ? 'Showing {n} of {total} {w}' : 'Showing all {total} {w}');
        };
        count.textContent = (filtered ? fmt('data-sk-count-filtered') : fmt('data-sk-count-all'))
          .replace('{n}', shown)
          .replace('{total}', total)
          .replace('{w}', word);
      }
      if (empty) { empty.classList.toggle('is-on', shown === 0); }
    }

    function setGroup(value, btn) {
      state.group = value;
      chips.forEach(function (c) { c.setAttribute('aria-pressed', c === btn ? 'true' : 'false'); });
      apply();
    }

    if (input) {
      input.addEventListener('input', function () {
        state.q = input.value.toLowerCase().trim();
        apply();
      });
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { input.value = ''; state.q = ''; apply(); }
      });
    }
    if (diffBox) { diffBox.addEventListener('change', function () { state.diff = diffBox.checked; apply(); }); }
    // Every reset affordance clears everything: the toolbar's X and the
    // empty state's "Reset filters" both have to work, so bind them all
    // rather than just the first match.
    resets.forEach(function (btn) {
      btn.addEventListener('click', function () {
        state = { q: '', diff: false, group: '' };
        if (input) { input.value = ''; }
        if (diffBox) { diffBox.checked = false; }
        chips.forEach(function (c) {
          c.setAttribute('aria-pressed', (c.getAttribute('data-group') || '') === '' ? 'true' : 'false');
        });
        apply();
        // Returning focus to the box means you can retype straight away.
        if (input) { input.focus(); }
      });
    });
    chips.forEach(function (c) {
      c.addEventListener('click', function () { setGroup(c.getAttribute('data-group') || '', c); });
    });

    // "/" focuses search, the way every data tool on the internet behaves.
    document.addEventListener('keydown', function (e) {
      if (input && e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName)) {
        e.preventDefault();
        input.focus();
        input.select();
      }
    });

    apply();
  })();

  /* ---------------------------------------------------------- *
   * Scroll reveal  (no-op without IntersectionObserver)
   * ---------------------------------------------------------- */
  (function reveal() {
    var items = $$('[data-sk-reveal]');
    if (!items.length) { return; }
    if (reduceMotion || !('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-in'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) { return; }
        entry.target.classList.add('is-in');
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });
    items.forEach(function (el) { io.observe(el); });
  })();
})();
</script>
