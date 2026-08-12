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
        var href = link.getAttribute('href') || '';
        var hashIndex = href.indexOf('#');
        var id = hashIndex >= 0 ? href.slice(hashIndex + 1) : href.replace(/^#/, '');
        return id ? document.getElementById(id) : null;
      });
      var sideNavPositions = [];
      var sideNavTicking = false;
      var pageHeight = 0;

      var measureSideNav = function () {
        var scrollY = window.lenis ? window.lenis.scroll : window.scrollY;
        pageHeight = document.documentElement.scrollHeight;
        sideNavSections.forEach(function (sec, i) {
          if (!sec) { sideNavPositions[i] = Infinity; return; }
          var start = NaN;
          if (window.ScrollTrigger) {
            var triggers = window.ScrollTrigger.getAll();
            for (var j = 0; j < triggers.length; j++) {
              var t = triggers[j];
              if (!t.vars || !t.vars.pin) { continue; }
              var triggerEl = (t.trigger && t.trigger.nodeType === 1) ? t.trigger : null;
              if (triggerEl === sec ||
                  (sec.id && triggerEl && triggerEl.id === sec.id)) {
                start = t.start;
                break;
              }
            }
          }
          if (isNaN(start)) {
            start = sec.getBoundingClientRect().top + scrollY;
          }
          sideNavPositions[i] = start;
        });
      };

      var updateSideNav = function () {
        var scrollY = window.lenis ? window.lenis.scroll : window.scrollY;
        var mid = window.innerHeight / 2;
        var active = 0;
        for (var i = 0; i < sideNavSections.length; i++) {
          if (!sideNavSections[i]) { continue; }
          if (sideNavPositions[i] <= scrollY + mid && sideNavPositions[i] >= 0) {
            active = i;
          }
        }
        sideNavLinks.forEach(function (link, i) {
          link.classList.toggle('is-active', i === active);
        });

        if (sideNavProgress && pageHeight > window.innerHeight) {
          var max = pageHeight - window.innerHeight;
          var p = max > 0 ? Math.min(1, scrollY / max) : 0;
          sideNavProgress.classList.toggle('is-filled', p > 0.02);
          sideNavProgress.style.setProperty('--p', p);
        }
      };

      var scheduleSideNavUpdate = function () {
        if (sideNavTicking) { return; }
        sideNavTicking = true;
        requestAnimationFrame(function () {
          updateSideNav();
          sideNavTicking = false;
        });
      };

      measureSideNav();
      var scheduleMeasure = function () {
        measureSideNav();
        updateSideNav();
      };
      if (document.readyState === 'complete') {
        scheduleMeasure();
      } else {
        window.addEventListener('load', scheduleMeasure, { once: true });
        scheduleMeasure();
      }
      window.addEventListener('resize', scheduleMeasure, { passive: true });
      window.addEventListener('seraph:scroll-ready', scheduleMeasure, { once: true });
      if (window.ScrollTrigger) {
        window.ScrollTrigger.addEventListener('refresh', scheduleMeasure);
      }

      if (window.lenis) {
        window.lenis.on('scroll', scheduleSideNavUpdate);
      } else {
        window.addEventListener('scroll', scheduleSideNavUpdate, { passive: true });
      }
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
      var topbarTicking = false;
      var onScroll = function () {
        if (topbarTicking) { return; }
        topbarTicking = true;
        requestAnimationFrame(function () {
          var y = window.lenis ? window.lenis.scroll : window.scrollY;
          topbar.classList.toggle('is-scrolled', y > 20);
          topbarTicking = false;
        });
      };
      if (window.lenis) {
        window.lenis.on('scroll', onScroll);
      } else {
        window.addEventListener('scroll', onScroll, { passive: true });
      }
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

      /* Keep keyboard focus inside the open mobile menu. */
      mobileMenu.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab' || !mobileMenu.classList.contains('is-open')) { return; }
        var focusables = mobileMenu.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])');
        if (!focusables.length) { return; }
        var first = focusables[0];
        var last = focusables[focusables.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      });
    }

    /* ---------- Sign In modal (Client / Admin tabs) ---------- */
    var loginModal = document.getElementById('loginModal');
    var loginToggle = document.getElementById('loginToggle');

    if (loginModal && loginToggle) {
      var loginOpeners = document.querySelectorAll('[data-login-open]');
      var loginClosers = document.querySelectorAll('[data-login-close]');
      var loginTabs = document.querySelectorAll('[data-login-tab]');
      var lastFocused = null;

      var setLoginOpen = function (open) {
        loginModal.classList.toggle('is-open', open);
        loginModal.setAttribute('aria-hidden', String(!open));
        document.body.style.overflow = open ? 'hidden' : '';
        if (window.lenis) {
          if (open) { window.lenis.stop(); } else { window.lenis.start(); }
        }
        if (open) {
          lastFocused = document.activeElement;
          var firstInput = loginModal.querySelector('.login-modal__pane.is-active input[type="email"]');
          if (firstInput) { firstInput.focus(); }
        } else if (lastFocused && lastFocused.focus) {
          lastFocused.focus();
        }
      };

      var setLoginTab = function (name) {
        loginTabs.forEach(function (tab) {
          var active = tab.getAttribute('data-login-tab') === name;
          tab.classList.toggle('is-active', active);
          tab.setAttribute('aria-selected', String(active));
        });
        loginModal.querySelectorAll('[data-login-pane]').forEach(function (pane) {
          pane.classList.toggle('is-active', pane.getAttribute('data-login-pane') === name);
        });
        var activePane = loginModal.querySelector('[data-login-pane="' + name + '"]');
        if (activePane) {
          var firstInput = activePane.querySelector('input[type="email"]');
          if (firstInput) { firstInput.focus(); }
        }
      };

      loginOpeners.forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (mobileMenu && mobileMenu.classList.contains('is-open') && menuToggle) {
            setMenu(false);
          }
          setLoginOpen(true);
        });
      });

      loginClosers.forEach(function (el) {
        el.addEventListener('click', function () {
          setLoginOpen(false);
        });
      });

      loginTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          setLoginTab(tab.getAttribute('data-login-tab'));
        });
        tab.addEventListener('keydown', function (e) {
          if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') { return; }
          e.preventDefault();
          var tabs = Array.prototype.slice.call(loginTabs);
          var idx = tabs.indexOf(tab);
          var next = e.key === 'ArrowRight' ? tabs[(idx + 1) % tabs.length] : tabs[(idx - 1 + tabs.length) % tabs.length];
          setLoginTab(next.getAttribute('data-login-tab'));
          next.focus();
        });
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && loginModal.classList.contains('is-open')) {
          setLoginOpen(false);
          if (loginToggle) { loginToggle.focus(); }
        }
      });

      /* Simple focus trap so Tab stays inside the dialog. */
      loginModal.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab' || !loginModal.classList.contains('is-open')) { return; }
        var focusables = loginModal.querySelectorAll('button, input, a[href]');
        if (!focusables.length) { return; }
        var first = focusables[0];
        var last = focusables[focusables.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
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
        newsletterStatus.classList.add('newsletter-status--error');
        newsletterStatus.textContent = 'Newsletter signup is not available yet. Please use the contact form instead.';
        return;
      });

      newsletterEmail.addEventListener('input', function () {
        newsletterEmail.removeAttribute('aria-invalid');
      });
    }

    /* ---------- Contact / quote form ---------- */
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
      var contactFields = {
        full_name: document.getElementById('contactFullName'),
        email: document.getElementById('contactEmail'),
        phone: document.getElementById('contactPhone'),
        service_type: document.getElementById('contactService'),
        message: document.getElementById('contactMessage')
      };
      var contactErrors = {
        full_name: document.getElementById('contactFullNameError'),
        email: document.getElementById('contactEmailError'),
        phone: document.getElementById('contactPhoneError'),
        service_type: document.getElementById('contactServiceError'),
        message: document.getElementById('contactMessageError')
      };
      var messageCount = document.getElementById('contactMessageCount');
      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

      function setContactError(field, message) {
        if (!field || !contactErrors[field]) return;
        contactFields[field].setAttribute('aria-invalid', message ? 'true' : 'false');
        contactErrors[field].textContent = message || '';
      }

      function clearContactErrors() {
        Object.keys(contactFields).forEach(function (key) {
          setContactError(key, '');
        });
      }

      function validateContactForm() {
        clearContactErrors();
        var valid = true;
        var name = contactFields.full_name.value.trim();
        var email = contactFields.email.value.trim();
        var phone = contactFields.phone.value.trim();
        var message = contactFields.message.value.trim();
        var digits = phone.replace(/\D/g, '');

        if (!name) {
          setContactError('full_name', 'Full name is required.');
          valid = false;
        } else if (name.length < 2) {
          setContactError('full_name', 'Full name must be at least 2 characters.');
          valid = false;
        }

        if (!email) {
          setContactError('email', 'Email address is required.');
          valid = false;
        } else if (!emailPattern.test(email)) {
          setContactError('email', 'Please enter a valid email address.');
          valid = false;
        }

        if (!phone) {
          setContactError('phone', 'Phone number is required.');
          valid = false;
        } else if (digits.length < 10 || digits.length > 15) {
          setContactError('phone', 'Please enter a valid phone number (10–15 digits).');
          valid = false;
        }

        if (!message) {
          setContactError('message', 'Message is required.');
          valid = false;
        } else if (message.length < 20) {
          setContactError('message', 'Message must be at least 20 characters.');
          valid = false;
        } else if (message.length > 5000) {
          setContactError('message', 'Message must be 5000 characters or fewer.');
          valid = false;
        }

        if (!valid) {
          var firstInvalid = contactForm.querySelector('[aria-invalid="true"]');
          if (firstInvalid) firstInvalid.focus();
        }

        return valid;
      }

      function updateMessageCount() {
        if (!messageCount || !contactFields.message) return;
        messageCount.textContent = contactFields.message.value.length + ' / 5000';
      }

      contactForm.addEventListener('submit', function (e) {
        if (!validateContactForm()) {
          e.preventDefault();
          return;
        }
        var submitBtn = contactForm.querySelector('.contact-form__submit');
        if (submitBtn && !submitBtn.disabled) {
          submitBtn.disabled = true;
          submitBtn.setAttribute('aria-busy', 'true');
          submitBtn.textContent = 'Sending...';
        }
      });

      Object.keys(contactFields).forEach(function (key) {
        var field = contactFields[key];
        if (!field) return;
        field.addEventListener('input', function () {
          setContactError(key, '');
        });
        field.addEventListener('blur', function () {
          validateContactForm();
        });
      });

      if (contactFields.message) {
        contactFields.message.addEventListener('input', updateMessageCount);
        updateMessageCount();
      }
    }
  });
})();
