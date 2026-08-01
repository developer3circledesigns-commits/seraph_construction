/* =====================================================
   SERAPH BUILD CONSTRUCTION — Main Scripts
   Layout 13 (Blur to Focus) only
   ===================================================== */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Side nav active section + progress ---------- */
    var sideNavLinks = document.querySelectorAll('[data-side-nav]');
    var sideNavProgress = document.getElementById('sideNavProgress');

    if (sideNavLinks.length) {
      var sideNavSections = Array.prototype.map.call(sideNavLinks, function (link) {
        var id = link.getAttribute('href');
        return id ? document.getElementById(id.slice(1)) : null;
      });

      var updateSideNav = function () {
        var mid = window.innerHeight / 2;
        var active = 0;
        sideNavSections.forEach(function (sec, i) {
          if (sec && sec.getBoundingClientRect().top <= mid) {
            active = i;
          }
        });
        sideNavLinks.forEach(function (link, i) {
          link.classList.toggle('is-active', i === active);
        });

        if (sideNavProgress && document.documentElement.scrollHeight > window.innerHeight) {
          var max = document.documentElement.scrollHeight - window.innerHeight;
          var p = max > 0 ? Math.min(1, window.scrollY / max) : 0;
          sideNavProgress.classList.toggle('is-filled', p > 0.02);
          sideNavProgress.style.setProperty('--p', p);
        }
      };

      window.addEventListener('scroll', updateSideNav, { passive: true });
      updateSideNav();
    }

    /* ---------- Current year ---------- */
    var yearEl = document.getElementById('year');
    if (yearEl) {
      yearEl.textContent = new Date().getFullYear();
    }

    /* ---------- Topbar scroll state ---------- */
    var topbar = document.querySelector('.topbar');
    if (topbar) {
      var onScroll = function () {
        if (window.scrollY > 20) {
          topbar.classList.add('is-scrolled');
        } else {
          topbar.classList.remove('is-scrolled');
        }
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    /* ---------- Mobile menu ---------- */
    var menuToggle = document.getElementById('menuToggle');
    var mobileMenu = document.getElementById('mobileMenu');

    if (menuToggle && mobileMenu) {
      var mobileMenuLinks = mobileMenu.querySelectorAll('.mobile-menu__link, .mobile-menu__quote');

      var setMenu = function (open) {
        mobileMenu.classList.toggle('is-open', open);
        menuToggle.setAttribute('aria-expanded', String(open));
        mobileMenu.setAttribute('aria-hidden', String(!open));
        menuToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        document.body.style.overflow = open ? 'hidden' : '';
        if (window.lenis) {
          if (open) { window.lenis.stop(); } else { window.lenis.start(); }
        }
        if (open) {
          var first = mobileMenu.querySelector('.mobile-menu__link, .mobile-menu__quote');
          if (first) {
            first.focus();
          }
        } else {
          menuToggle.focus();
        }
      };

      menuToggle.addEventListener('click', function () {
        setMenu(!mobileMenu.classList.contains('is-open'));
      });

      mobileMenuLinks.forEach(function (link) {
        link.addEventListener('click', function () {
          setMenu(false);
        });
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
          setMenu(false);
          menuToggle.focus();
        }
      });
    }

    /* ---------- Materials filter (horizontal scroll) ---------- */
    var filterButtons = document.querySelectorAll('.materials-filter__btn');
    var materialCards = document.querySelectorAll('.material-card');

    var applyFilter = function (filter) {
      var showAll = filter === 'all';
      materialCards.forEach(function (card) {
        var show = showAll || card.getAttribute('data-category') === filter;
        card.classList.toggle('is-hidden', !show);
      });

      filterButtons.forEach(function (btn) {
        var active = btn.getAttribute('data-filter') === filter;
        btn.classList.toggle('active', active);
        btn.setAttribute('aria-pressed', String(active));
      });

      if (window.ScrollTrigger) {
        window.ScrollTrigger.refresh();
      }
      window.dispatchEvent(new Event('materials-filter-updated'));
    };

    filterButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        applyFilter(btn.getAttribute('data-filter'));
      });
    });

    /* In-page links that target a filter */
    document.querySelectorAll('[data-filter-target]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        var filter = link.getAttribute('data-filter-target');
        var targetBtn = document.querySelector('.materials-filter__btn[data-filter="' + filter + '"]');
        if (targetBtn) {
          e.preventDefault();
          applyFilter(filter);
          var materialsSection = document.getElementById('materials');
          if (materialsSection) {
            if (window.lenis) {
              window.lenis.scrollTo(materialsSection, { offset: 0, duration: 1.3 });
            } else {
              materialsSection.scrollIntoView({ behavior: 'smooth' });
            }
          }
        }
      });
    });

    /* ---------- Testimonials 01 carousel ---------- */
    var quotesCarousel = document.getElementById('quotesCarousel');
    if (quotesCarousel) {
      var slides = Array.prototype.slice.call(quotesCarousel.querySelectorAll('.slide'));
      var dotsWrap = document.getElementById('dots');
      var prevBtn = document.getElementById('prevBtn');
      var nextBtn = document.getElementById('nextBtn');
      var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var index = 0;

      slides.forEach(function (s, i) {
        var d = document.createElement('span');
        d.classList.toggle('is-active', i === 0);
        d.addEventListener('click', function () { go(i); });
        dotsWrap.appendChild(d);
      });

      function go(i) {
        index = (i + slides.length) % slides.length;
        slides.forEach(function (s, k) { s.classList.toggle('is-active', k === index); });
        Array.prototype.forEach.call(dotsWrap.children, function (d, k) { d.classList.toggle('is-active', k === index); });
        if (!reduceMotion && window.gsap) {
          gsap.fromTo(slides[index], { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' });
          gsap.fromTo(slides[index].querySelectorAll('blockquote,.who,.role'), { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.7, stagger: 0.1, ease: 'power2.out' });
        }
      }

      if (nextBtn) { nextBtn.addEventListener('click', function () { go(index + 1); }); }
      if (prevBtn) { prevBtn.addEventListener('click', function () { go(index - 1); }); }

      if (!reduceMotion) {
        setInterval(function () { go(index + 1); }, 6000);
      }
    }

    /* ---------- Newsletter form ---------- */
    var newsletterForm = document.getElementById('newsletterForm');
    var newsletterEmail = document.getElementById('newsletterEmail');
    var newsletterStatus = document.getElementById('newsletterStatus');

    if (newsletterForm && newsletterEmail && newsletterStatus) {
      newsletterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var value = newsletterEmail.value.trim();
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        var emailValid = emailPattern.test(value);

        newsletterStatus.classList.remove('newsletter-status--error', 'newsletter-status--success');

        if (!value) {
          newsletterEmail.setAttribute('aria-invalid', 'true');
          newsletterStatus.textContent = 'Please enter your email address.';
          newsletterStatus.classList.add('newsletter-status--error');
          return;
        }

        if (!emailValid) {
          newsletterEmail.setAttribute('aria-invalid', 'true');
          newsletterStatus.textContent = 'Please enter a valid email address.';
          newsletterStatus.classList.add('newsletter-status--error');
          return;
        }

        newsletterEmail.removeAttribute('aria-invalid');
        newsletterStatus.classList.add('newsletter-status--success');
        newsletterStatus.textContent = 'Thank you! You are now subscribed.';
        newsletterForm.reset();
      });

      newsletterEmail.addEventListener('input', function () {
        newsletterEmail.removeAttribute('aria-invalid');
      });
    }
  });
})();
