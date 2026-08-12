/* =====================================================
   SERAPH Construction — JS Animations
   Layout 13 (Blur to Focus) — GSAP + ScrollTrigger
   Performance: avoid scrubbing CSS filter (GPU-heavy);
   no snap on long pinned timelines; throttle layout reads.
   ===================================================== */

(function () {
  'use strict';

  function initAnimations() {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion) {
      document.querySelectorAll('.blur-panel__media img, .blur-card img, .testimonials-section__bg img').forEach(function (img) {
        img.style.filter = 'none';
        img.style.transform = 'none';
      });
      document.querySelectorAll('.projects-story__step').forEach(function (s, i) {
        s.style.opacity = i === 0 ? 1 : 0;
      });
      document.querySelectorAll('.projects-story__visual img').forEach(function (img, i) {
        img.classList.toggle('active', i === 0);
      });
      window.dispatchEvent(new CustomEvent('seraph:animations-ready'));
      return;
    }

    if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') {
      window.dispatchEvent(new CustomEvent('seraph:animations-ready'));
      return;
    }

    gsap.registerPlugin(ScrollTrigger);

    /* Full-bleed panels: scrub scale/opacity only (not filter — causes scroll jank). */
    gsap.utils.toArray('[data-blur]').forEach(function (panel) {
      var img = panel.querySelector('img');
      if (!img) {
        return;
      }

      gsap.fromTo(img,
        { scale: 1.12, opacity: 0.88 },
        {
          scale: 1,
          opacity: 1,
          ease: 'none',
          scrollTrigger: {
            trigger: panel,
            start: 'top bottom',
            end: 'center center',
            scrub: 0.35,
            fastScrollEnd: true,
          },
        }
      );

      var content = panel.querySelector('.blur-panel__content');
      if (content) {
        gsap.from(content, {
          opacity: 0,
          y: 40,
          duration: 0.9,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: panel,
            start: 'top 70%',
            toggleActions: 'play none none none',
          },
        });
      }
    });

    /* Hero title line reveal */
    var heroLines = gsap.utils.toArray('.hero__title .line-inner');
    if (heroLines.length) {
      gsap.set(heroLines, { yPercent: 120 });
      gsap.to(heroLines, {
        yPercent: 0,
        duration: 1,
        ease: 'power4.out',
        stagger: 0.12,
        delay: 0.15,
      });
    }

    /* Split blur cards: one-shot reveal (no scrubbed filter). */
    var cards = gsap.utils.toArray('.blur-card');
    cards.forEach(function (card) {
      var img = card.querySelector('img');
      if (img) {
        gsap.fromTo(img,
          { scale: 1.08, opacity: 0.9 },
          {
            scale: 1,
            opacity: 1,
            duration: 1,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: card,
              start: 'top 75%',
              toggleActions: 'play none none none',
            },
          }
        );
      }

      var text = card.querySelector(':scope > div');
      if (text) {
        gsap.from(text, {
          opacity: 0,
          x: 60,
          duration: 1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: card,
            start: 'top 75%',
            toggleActions: 'play none none none',
          },
        });
      }
    });

    /* Kitchen — pinned image sequence (no snap — snap + Lenis caused sticking). */
    var kitchenSection = document.getElementById('homeplan');
    if (kitchenSection) {
      var kitchenImgs = gsap.utils.toArray('.kitchen-sequence__img');
      var kitchenStep = document.getElementById('kitchenStepCurrent');
      var kitchenDots = gsap.utils.toArray('.kitchen-sequence__dots span');
      var kitchenCopies = gsap.utils.toArray('.kitchen-sequence__copy');

      if (kitchenImgs.length >= 2) {
        gsap.set(kitchenImgs[0], { autoAlpha: 1, scale: 1 });
        gsap.set(kitchenImgs.slice(1), { autoAlpha: 0, scale: 1.08 });
        gsap.set(kitchenCopies, { autoAlpha: 0 });
        gsap.set(kitchenCopies[0], { autoAlpha: 1 });

        var kitchenTl = gsap.timeline({
          scrollTrigger: {
            trigger: kitchenSection,
            start: 'top top',
            end: '+=400%',
            pin: true,
            scrub: 0.5,
            anticipatePin: 1,
            fastScrollEnd: true,
            invalidateOnRefresh: true,
          },
        });

        kitchenTl.eventCallback('onUpdate', function () {
          var step = Math.min(kitchenImgs.length, 1 + Math.floor((kitchenTl.time() + 0.3) / 0.8));
          if (kitchenStep) {
            kitchenStep.textContent = step;
          }
          if (kitchenDots.length) {
            kitchenDots.forEach(function (dot, i) {
              dot.classList.toggle('is-active', i < step);
            });
          }
        });

        kitchenImgs.slice(1).forEach(function (img, i) {
          kitchenTl.to(img, {
            autoAlpha: 1,
            scale: 1,
            duration: 1,
            ease: 'power2.out',
          }, i * 0.8);

          kitchenTl.to(kitchenImgs[i], {
            scale: 0.97,
            autoAlpha: 0.4,
            duration: 1,
            ease: 'power1.in',
          }, i * 0.8);

          if (kitchenCopies.length) {
            kitchenTl.to(kitchenCopies[i], {
              autoAlpha: 0,
              y: -12,
              duration: 1,
              ease: 'power2.out',
            }, i * 0.8);

            kitchenTl.to(kitchenCopies[i + 1], {
              autoAlpha: 1,
              y: 0,
              duration: 1,
              ease: 'power2.out',
            }, i * 0.8);
          }
        });

        kitchenTl.to({}, { duration: 0.6 });
      }
    }

    /* Materials — pinned horizontal scroll */
    var materialsSection = document.querySelector('.materials-section');
    var materialsTrack = document.getElementById('materialsTrack');
    var materialsProgress = document.getElementById('materialsProgress');

    if (materialsSection && materialsTrack) {
      var materialsCards = gsap.utils.toArray(materialsTrack.querySelectorAll('.material-card'));
      var materialsBg = materialsSection.querySelector('.materials-bg');

      var materialsBgImgs = [];
      if (materialsBg) {
        materialsCards.forEach(function (card) {
          var cardImg = card.querySelector('.material-card__img-wrap img');
          if (cardImg) {
            var bgImg = document.createElement('img');
            bgImg.src = cardImg.getAttribute('src');
            bgImg.alt = '';
            bgImg.loading = 'lazy';
            bgImg.decoding = 'async';
            materialsBg.appendChild(bgImg);
            materialsBgImgs.push(bgImg);
          }
        });
      }

      var materialsBgPending = false;
      var materialsBgIndex = 0;

      var setMaterialsBg = function () {
        if (!materialsBgImgs.length) {
          return;
        }
        var viewportCenter = window.innerWidth / 2;
        var best = 0;
        var bestDist = Infinity;
        materialsCards.forEach(function (card, i) {
          if (card.classList.contains('is-hidden')) {
            return;
          }
          var rect = card.getBoundingClientRect();
          if (rect.right < 0 || rect.left > window.innerWidth) {
            return;
          }
          var dist = Math.abs(rect.left + rect.width / 2 - viewportCenter);
          if (dist < bestDist) {
            bestDist = dist;
            best = i;
          }
        });
        if (best === materialsBgIndex) {
          return;
        }
        materialsBgIndex = best;
        materialsBgImgs.forEach(function (bgImg, i) {
          bgImg.classList.toggle('is-active', i === best);
        });
      };

      var scheduleMaterialsBg = function () {
        if (materialsBgPending) {
          return;
        }
        materialsBgPending = true;
        requestAnimationFrame(function () {
          materialsBgPending = false;
          setMaterialsBg();
        });
      };

      var getTrackDist = function () {
        return Math.max(0, materialsTrack.scrollWidth - window.innerWidth);
      };

      gsap.to(materialsTrack, {
        x: function () {
          return -getTrackDist();
        },
        ease: 'none',
        scrollTrigger: {
          trigger: materialsSection,
          start: 'top top',
          end: function () {
            return '+=' + Math.max(getTrackDist(), 1);
          },
          pin: true,
          scrub: 0.4,
          invalidateOnRefresh: true,
          anticipatePin: 1,
          fastScrollEnd: true,
          onUpdate: function (self) {
            if (materialsProgress) {
              materialsProgress.style.width = (self.progress * 100) + '%';
            }
            scheduleMaterialsBg();
          },
        },
      });

      setMaterialsBg();

      var resizeTimer;
      window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
          ScrollTrigger.refresh();
        }, 200);
      });

      window.addEventListener('materials-filter-updated', function () {
        materialsBgIndex = -1;
        if (window.ScrollTrigger) {
          ScrollTrigger.refresh(true);
        }
        scheduleMaterialsBg();
      });
    }

    /* Services — staggered scroll reveal */
    gsap.utils.toArray('.services-section .eyebrow, .services-section__heading, .services-section__intro, .services-grid').forEach(function (el) {
      gsap.from(el, {
        opacity: 0,
        y: 40,
        duration: 0.9,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: el,
          start: 'top 80%',
          toggleActions: 'play none none none',
        },
      });
    });

    gsap.utils.toArray('.service-card').forEach(function (card, i) {
      gsap.from(card, {
        opacity: 0,
        y: 50,
        duration: 0.8,
        ease: 'power3.out',
        delay: (i % 3) * 0.12,
        scrollTrigger: {
          trigger: card,
          start: 'top 90%',
          toggleActions: 'play none none none',
        },
      });
    });

    /* Projects — section heading reveal */
    var projectsHeadEls = gsap.utils.toArray('.projects-section__head .eyebrow, .projects-section__heading, .projects-section__intro');
    var projectsHeadTrigger = document.querySelector('.projects-section__head');
    if (projectsHeadEls.length && projectsHeadTrigger) {
      gsap.from(projectsHeadEls, {
        opacity: 0,
        y: 40,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.1,
        scrollTrigger: {
          trigger: projectsHeadTrigger,
          start: 'top 85%',
          toggleActions: 'play none none none',
        },
      });
    }

    /* Projects — Layout 06 split scrollytelling */
    var projectsStory = document.getElementById('projectsStory');
    if (projectsStory) {
      var pSteps = gsap.utils.toArray('.projects-story__step');
      var pImgs = gsap.utils.toArray('.projects-story__visual img');
      var pDots = gsap.utils.toArray('#projectsDots button');
      var pRail = document.getElementById('projectsRail');
      var pTotal = pSteps.length;

      var projectsBg = projectsStory.querySelector('.projects-bg');
      var projectsBgImgs = [];
      if (projectsBg) {
        pImgs.forEach(function (img) {
          var bgImg = document.createElement('img');
          bgImg.src = img.getAttribute('src');
          bgImg.alt = '';
          bgImg.loading = 'lazy';
          bgImg.decoding = 'async';
          projectsBg.appendChild(bgImg);
          projectsBgImgs.push(bgImg);
        });
      }

      var projectsBgIndex = 0;
      var setProjectsBg = function (idx) {
        if (!projectsBgImgs.length || idx === projectsBgIndex) {
          return;
        }
        projectsBgIndex = idx;
        projectsBgImgs.forEach(function (bgImg, i) {
          bgImg.classList.toggle('is-active', i === idx);
        });
      };

      if (pSteps.length >= 2) {
        gsap.set(pSteps, { opacity: 0 });
        gsap.set(pImgs, { opacity: 0 });
        gsap.set(pSteps[0], { opacity: 1 });
        gsap.set(pImgs[0], { opacity: 1 });
        setProjectsBg(0);

        var projectsInner = projectsStory.querySelector('.projects-story__inner');
        var pTl = gsap.timeline({
          scrollTrigger: {
            trigger: projectsStory,
            start: 'top top',
            end: 'bottom bottom',
            pin: projectsInner || true,
            scrub: 0.45,
            anticipatePin: 1,
            fastScrollEnd: true,
            invalidateOnRefresh: true,
          },
        });

        for (var pi = 1; pi < pTotal; pi++) {
          pTl.to(pSteps[pi - 1], { opacity: 0, y: -30, duration: 0.1, ease: 'power2.in' });
          pTl.to(pImgs[pi - 1], { opacity: 0, scale: 1, duration: 0.15, ease: 'power2.out' }, '<');
          pTl.fromTo(pSteps[pi], { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.18, ease: 'power2.out' });
          pTl.fromTo(pImgs[pi], { opacity: 0, scale: 1.06 }, { opacity: 1, scale: 1, duration: 0.22, ease: 'power2.out' }, '<');
        }

        ScrollTrigger.create({
          trigger: projectsStory,
          start: 'top top',
          end: 'bottom bottom',
          onUpdate: function (self) {
            if (pRail) {
              pRail.style.height = (self.progress * 100) + '%';
            }
            var idx = Math.min(pTotal - 1, Math.floor(self.progress * pTotal));
            pDots.forEach(function (d, i) {
              d.classList.toggle('active', i === idx);
            });
            setProjectsBg(idx);
          },
        });

        pDots.forEach(function (dot, i) {
          dot.addEventListener('click', function () {
            var st = ScrollTrigger.getAll().find(function (t) {
              return t.trigger && t.trigger.id === 'projectsStory';
            });
            if (st) {
              var target = st.start + (st.end - st.start) * ((i + 0.5) / pTotal);
              if (window.lenis) {
                window.lenis.scrollTo(target, { duration: 1.1 });
              } else {
                window.scrollTo({ top: target, behavior: 'smooth' });
              }
            }
          });
        });
      }
    }

    /* About — heading line reveal */
    var aboutLines = gsap.utils.toArray('.about-head .line-inner');
    if (aboutLines.length) {
      gsap.set(aboutLines, { yPercent: 120 });
      gsap.to(aboutLines, {
        yPercent: 0,
        duration: 1,
        ease: 'power4.out',
        stagger: 0.12,
        delay: 0.1,
      });
    }

    gsap.utils.toArray('[data-reveal]').forEach(function (el) {
      gsap.from(el, {
        opacity: 0,
        y: 40,
        duration: 0.9,
        ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none none' },
      });
    });

    /* About split media — scale/opacity only */
    gsap.utils.toArray('[data-img] img').forEach(function (img) {
      gsap.fromTo(img,
        { scale: 1.06, opacity: 0.92 },
        {
          scale: 1,
          opacity: 1,
          duration: 1.2,
          ease: 'power2.out',
          scrollTrigger: { trigger: img, start: 'top 85%', toggleActions: 'play none none none' },
        }
      );
    });

    var statsGrid = document.querySelector('.stats-grid');
    var statEls = gsap.utils.toArray('.stats-grid .stat');
    if (statEls.length && statsGrid) {
      gsap.from(statEls, {
        opacity: 0,
        y: 50,
        duration: 0.8,
        stagger: 0.15,
        ease: 'power2.out',
        scrollTrigger: { trigger: statsGrid, start: 'top 85%', toggleActions: 'play none none none' },
      });
    }

    /* Footer marquee */
    var marqueeTrack = document.getElementById('marqueeTrack');
    var footerEl = document.querySelector('.footer-02');
    if (marqueeTrack && footerEl) {
      gsap.to(marqueeTrack, {
        xPercent: -50,
        ease: 'none',
        scrollTrigger: {
          trigger: footerEl,
          start: 'top bottom',
          end: 'bottom bottom',
          scrub: 0.5,
          fastScrollEnd: true,
        },
      });
    }

    /* Animated counters */
    gsap.utils.toArray('.counter').forEach(function (counter) {
      var target = parseInt(counter.getAttribute('data-target'), 10);
      if (isNaN(target)) {
        return;
      }

      var obj = { value: 0 };
      gsap.to(obj, {
        value: target,
        duration: 2,
        ease: 'power1.out',
        scrollTrigger: {
          trigger: counter,
          start: 'top 90%',
          toggleActions: 'play none none none',
          onUpdate: function () {
            counter.textContent = Math.floor(obj.value);
          },
        },
      });
    });

    /* Let images settle, then refresh pin spacers before Lenis starts. */
    requestAnimationFrame(function () {
      ScrollTrigger.refresh(true);
      window.dispatchEvent(new CustomEvent('seraph:animations-ready'));
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAnimations);
  } else {
    initAnimations();
  }
})();
