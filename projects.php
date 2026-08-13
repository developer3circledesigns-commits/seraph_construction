<?php
/**
 * SERAPH BUILD CONSTRUCTION — Projects page.
 * Separate portfolio page: On Going Projects + Completed Projects.
 * Now database-driven from the projects table.
 */
$site = require __DIR__ . '/config/site.php';
require __DIR__ . '/api/config/bootstrap.php';

$allProjects = Project::publicList();

$ongoing   = array_filter($allProjects, fn($p) => $p['status'] === 'in_progress');
$completed = array_filter($allProjects, fn($p) => $p['status'] === 'completed');

require __DIR__ . '/partials/header.php';

function projectThumbnail(array $p): string
{
    if (!empty($p['thumbnail']) && file_exists(__DIR__ . '/' . $p['thumbnail'])) {
        return $p['thumbnail'];
    }
    return 'images/projects/theme-home-daylight-warm.webp';
}

function fmtSpec(?string $value): string
{
    return $value !== null && $value !== '' ? e($value) : '—';
}

function fmtInt(?int $value): string
{
    return $value !== null ? (string)$value : '—';
}

function truncateDesc(string $text, int $length = 80): array
{
    if (mb_strlen($text) <= $length) {
        return ['text' => e($text), 'truncated' => false];
    }
    $cut = mb_substr($text, 0, $length);
    $lastSpace = mb_strrpos($cut, ' ');
    if ($lastSpace !== false && $lastSpace > $length * 0.6) {
        $cut = mb_substr($cut, 0, $lastSpace);
    }
    return ['text' => e($cut), 'truncated' => true];
}
?>
<main id="main-content" class="projects-page">
  <style>
    .projects-page { padding: 7rem 0 5rem; }
    .projects-page__head { max-width: 1320px; margin: 0 auto; padding: 0 2rem; }
    .projects-page__head .eyebrow { color: #C79A56; font-size: 0.78rem; letter-spacing: 0.35em; text-transform: uppercase; font-weight: 600; }
    .projects-page__head h1 { font-family: 'Fraunces', Georgia, serif; font-weight: 400; font-size: clamp(2.4rem, 5vw, 4rem); color: #f4efe8; margin: 0.8rem 0 1rem; }
    .projects-page__head p { max-width: 620px; color: #a29a8c; }

    .projects-page__category { max-width: 1320px; margin: 4.5rem auto 0; padding: 0 2rem; }

    .projects-filter { display: flex; justify-content: center; gap: 0.6rem; margin: 2.5rem 0 0.5rem; flex-wrap: wrap; }
    .projects-filter__btn { padding: 0.65rem 1.6rem; border: 1px solid #3a352c; border-radius: 999px; background: transparent; color: #c9c2b5; font-family: inherit; font-size: 0.85rem; letter-spacing: 0.05em; cursor: pointer; transition: all 0.3s ease; }
    .projects-filter__btn:hover { border-color: #C79A56; color: #f4efe8; }
    .projects-filter__btn.is-active { background: #C79A56; border-color: #C79A56; color: #141210; font-weight: 600; }
    .projects-filter__btn:focus-visible { outline: 2px solid #C79A56; outline-offset: 2px; }
    .projects-page__category-label { display: flex; align-items: center; gap: 1.2rem; margin-bottom: 2.2rem; }
    .projects-page__category-label h2 { font-family: 'Fraunces', Georgia, serif; font-weight: 400; font-size: clamp(1.6rem, 3vw, 2.2rem); color: #f4efe8; }
    .projects-page__category-label span { font-size: 0.85rem; letter-spacing: 0.15em; text-transform: uppercase; color: #C79A56; }
    .projects-page__category-label::after { content: ''; flex: 1; height: 1px; background: #2a261f; }

    .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.1rem; }
    .project-card { position: relative; display: flex; flex-direction: column; overflow: hidden; border-radius: 4px; background: #16140f; text-decoration: none; color: inherit; height: 100%; }
    .project-card__link { display: block; text-decoration: none; color: inherit; }
    .project-card__link .project-card__title { padding: 0.75rem 0.9rem 0; }
    .project-card__media { aspect-ratio: 16 / 11; overflow: hidden; }
    .project-card__media img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.2, 0.6, 0.2, 1); }
    .project-card__link:hover .project-card__media img { transform: scale(1.05); }
    .project-card__body { padding: 0.9rem 0.9rem 0.9rem; flex: 1; }
    .project-card__no { font-size: 0.66rem; letter-spacing: 0.18em; text-transform: uppercase; color: #C79A56; }
    .project-card__title { font-family: 'Fraunces', Georgia, serif; font-size: 1.1rem; font-weight: 400; margin: 0.3rem 0 0.25rem; color: #f4efe8; }
    .project-card__text { font-size: 0.82rem; color: #a29a8c; margin-bottom: 0.7rem; }
    .project-card__desc-toggle { color: #C79A56; cursor: pointer; font-size: 0.78rem; font-weight: 500; letter-spacing: 0.04em; display: inline-block; margin-top: 0.2rem; background: none; border: none; padding: 0; font-family: inherit; }
    .project-card__desc-toggle:hover { color: #e0b376; text-decoration: underline; }
    .project-card__desc-toggle:focus-visible { outline: 2px solid #C79A56; outline-offset: 2px; border-radius: 2px; }
    .project-card__desc-full { display: none; }
    .project-card__desc-full.is-open { display: inline; }
    .project-card__desc-cut.is-hidden { display: none; }
    .project-card__arrow { position: absolute; top: 0.8rem; right: 0.8rem; width: 34px; height: 34px; border-radius: 50%; background: #C79A56; color: #141210; display: grid; place-items: center; opacity: 0; transform: translateY(-8px); transition: all 0.3s ease; font-size: 0.85rem; }
    .project-card:hover .project-card__arrow { opacity: 1; transform: translateY(0); }

    .project-card__specs { display: grid; grid-template-columns: 1fr 1fr; gap: 0.45rem 0.8rem; border-top: 1px solid #2a261f; padding-top: 0.7rem; }
    .project-card__spec { display: flex; justify-content: space-between; align-items: baseline; gap: 0.4rem; font-size: 0.72rem; line-height: 1.4; }
    .project-card__spec-label { color: #8a8377; letter-spacing: 0.04em; }
    .project-card__spec-value { color: #e9e5de; text-align: right; }

    .project-card__download { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1rem; margin: 0 0.9rem 0.9rem; border: 1px solid #3a352c; border-radius: 3px; background: transparent; color: #c9c2b5; font-family: inherit; font-size: 0.78rem; letter-spacing: 0.08em; text-transform: uppercase; text-decoration: none; transition: all 0.3s ease; }
    .project-card__download:hover { border-color: #C79A56; color: #C79A56; background: rgba(199,154,86,0.08); }

    .projects-page__cta { max-width: 1320px; margin: 5rem auto 0; padding: 0 2rem; text-align: center; }
    .btn--gold { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.9rem 2rem; background: #C79A56; color: #141210; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; font-size: 0.85rem; border-radius: 2px; text-decoration: none; transition: background 0.3s ease; }
    .btn--gold:hover { background: #e0b376; }

    .projects-empty { text-align: center; padding: 3rem; color: #a29a8c; }

    @media (max-width: 767px) {
      .projects-page { padding: 5.5rem 0 3rem; text-align: center; }
      .projects-page__head,
      .projects-page__category,
      .projects-page__cta { padding-left: 1rem; padding-right: 1rem; }
      .projects-page__head p { margin-left: auto; margin-right: auto; }
      .projects-page__category { margin-top: 3rem; }
      .projects-page__category-label {
        flex-wrap: wrap;
        gap: 0.6rem;
        justify-content: center;
        text-align: center;
      }
      .projects-page__category-label::after { display: none; }
      .projects-filter { gap: 0.5rem; margin-top: 1.5rem; padding: 0 1rem; justify-content: center; }
      .projects-filter__btn { padding: 0.75rem 1.1rem; font-size: 0.8rem; min-height: 44px; }
      .projects-grid { grid-template-columns: 1fr; gap: 1rem; }
      .project-card__body,
      .project-card__title,
      .project-card__text,
      .project-card__no { text-align: center; }
      .project-card__specs { grid-template-columns: 1fr; }
      .project-card__spec {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.15rem;
      }
      .project-card__spec-value { text-align: center; }
      .project-card__download { margin-left: 0.9rem; margin-right: 0.9rem; min-height: 44px; }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
      .projects-page__head,
      .projects-page__category,
      .projects-page__cta { padding-left: 1.5rem; padding-right: 1.5rem; }
      .projects-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>

  <div class="projects-page__head">
    <span class="eyebrow">Portfolio</span>
    <h1>Our Projects</h1>
    <p>From private villas to corporate headquarters — explore the work shaping our built environment today, and the projects already delivered.</p>
  </div>

  <?php if (empty($allProjects)): ?>
    <div class="projects-empty">
      <p>No projects to display at the moment. Check back soon.</p>
    </div>
  <?php else: ?>
  <div class="projects-filter" role="group" aria-label="Filter projects">
    <button type="button" class="projects-filter__btn" data-filter="ongoing" aria-pressed="false">On Going Projects</button>
    <button type="button" class="projects-filter__btn" data-filter="completed" aria-pressed="false">Completed Projects</button>
  </div>

  <?php if (!empty($ongoing)): ?>
  <section class="projects-page__category" id="ongoing" data-category="ongoing">
    <div class="projects-page__category-label">
      <h2>On Going Projects</h2>
      <span><?php echo count($ongoing); ?> project(s)</span>
    </div>
    <div class="projects-grid">
      <?php foreach ($ongoing as $i => $p): ?>
        <div class="project-card" title="<?php echo e($p['name']); ?>">
          <a class="project-card__link" href="contact.php">
            <div class="project-card__media"><img src="<?php echo e(projectThumbnail($p)); ?>" alt="<?php echo e($p['name']); ?>" loading="lazy"></div>
            <div class="project-card__arrow">&#8599;</div>
            <div class="project-card__title"><?php echo e($p['name']); ?></div>
          </a>
          <div class="project-card__body">
               <?php $desc = truncateDesc($p['description'] ?? ''); ?>
               <div class="project-card__text">
                 <span class="project-card__desc-cut"><?php echo $desc['text']; ?><?php if ($desc['truncated']): ?><span class="project-card__desc-dots">... <button type="button" class="project-card__desc-toggle" onclick="toggleDesc(event, this)">View more</button></span><?php endif; ?></span>
                 <?php if ($desc['truncated']): ?>
                 <span class="project-card__desc-full"> <?php echo e($p['description']); ?> <button type="button" class="project-card__desc-toggle" onclick="toggleDesc(event, this)">Show less</button></span>
                 <?php endif; ?>
               </div>
               <div class="project-card__specs">
                 <div class="project-card__spec"><span class="project-card__spec-label">Location</span><span class="project-card__spec-value"><?php echo fmtSpec($p['location']); ?></span></div>
                 <div class="project-card__spec"><span class="project-card__spec-label">Plot Size</span><span class="project-card__spec-value"><?php echo fmtSpec($p['plot_size']); ?></span></div>
                 <div class="project-card__spec"><span class="project-card__spec-label">Built-up Area</span><span class="project-card__spec-value"><?php echo fmtSpec($p['built_up_area']); ?></span></div>
                 <div class="project-card__spec"><span class="project-card__spec-label">Floors</span><span class="project-card__spec-value"><?php echo fmtInt($p['floors']); ?></span></div>
                 <div class="project-card__spec"><span class="project-card__spec-label">Bedrooms</span><span class="project-card__spec-value"><?php echo fmtInt($p['bedrooms']); ?></span></div>
                 <div class="project-card__spec"><span class="project-card__spec-label">Bathrooms</span><span class="project-card__spec-value"><?php echo fmtInt($p['bathrooms']); ?></span></div>
                 <div class="project-card__spec"><span class="project-card__spec-label">Style</span><span class="project-card__spec-value"><?php echo fmtSpec($p['style']); ?></span></div>
               </div>
             </div>
           <?php if ((int)$p['has_layout'] > 0): ?>
           <a class="project-card__download" href="/download-layout.php?id=<?php echo (int)$p['id']; ?>"><i class="fa-solid fa-download" aria-hidden="true"></i> Download Layout</a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($completed)): ?>
  <section class="projects-page__category" id="completed" data-category="completed">
    <div class="projects-page__category-label">
      <h2>Completed Projects</h2>
      <span><?php echo count($completed); ?> project(s)</span>
    </div>
    <div class="projects-grid">
      <?php foreach ($completed as $i => $p): ?>
        <div class="project-card" title="<?php echo e($p['name']); ?>">
          <a class="project-card__link" href="contact.php">
            <div class="project-card__media"><img src="<?php echo e(projectThumbnail($p)); ?>" alt="<?php echo e($p['name']); ?>" loading="lazy"></div>
            <div class="project-card__arrow">&#8599;</div>
            <div class="project-card__title"><?php echo e($p['name']); ?></div>
          </a>
          <div class="project-card__body">
               <?php $desc = truncateDesc($p['description'] ?? ''); ?>
               <div class="project-card__text">
                 <span class="project-card__desc-cut"><?php echo $desc['text']; ?><?php if ($desc['truncated']): ?><span class="project-card__desc-dots">... <button type="button" class="project-card__desc-toggle" onclick="toggleDesc(event, this)">View more</button></span><?php endif; ?></span>
                 <?php if ($desc['truncated']): ?>
                 <span class="project-card__desc-full"> <?php echo e($p['description']); ?> <button type="button" class="project-card__desc-toggle" onclick="toggleDesc(event, this)">Show less</button></span>
                 <?php endif; ?>
               </div>
              <div class="project-card__specs">
                <div class="project-card__spec"><span class="project-card__spec-label">Location</span><span class="project-card__spec-value"><?php echo fmtSpec($p['location']); ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Plot Size</span><span class="project-card__spec-value"><?php echo fmtSpec($p['plot_size']); ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Built-up Area</span><span class="project-card__spec-value"><?php echo fmtSpec($p['built_up_area']); ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Floors</span><span class="project-card__spec-value"><?php echo fmtInt($p['floors']); ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Bedrooms</span><span class="project-card__spec-value"><?php echo fmtInt($p['bedrooms']); ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Bathrooms</span><span class="project-card__spec-value"><?php echo fmtInt($p['bathrooms']); ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Style</span><span class="project-card__spec-value"><?php echo fmtSpec($p['style']); ?></span></div>
              </div>
            </div>
           <?php if ((int)$p['has_layout'] > 0): ?>
           <a class="project-card__download" href="/download-layout.php?id=<?php echo (int)$p['id']; ?>"><i class="fa-solid fa-download" aria-hidden="true"></i> Download Layout</a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
  <?php endif; ?>

  <div class="projects-page__cta">
    <a class="btn--gold" href="contact.php">Start Your Project</a>
  </div>
</main>

<script>
function toggleDesc(event, el) {
  event.stopPropagation ? event.stopPropagation() : event.cancelBubble = true;
  event.preventDefault ? event.preventDefault() : event.returnValue = false;
  var textEl = el.closest('.project-card__text');
  if (!textEl) return;
  var cut = textEl.querySelector('.project-card__desc-cut');
  var full = textEl.querySelector('.project-card__desc-full');
  if (!cut || !full) {
    // Fallback: if spans don't exist, create simple show/hide
    var fullEl = textEl.querySelector('.project-card__desc-full');
    if (fullEl) {
      if (fullEl.classList.contains('is-open')) {
        fullEl.classList.remove('is-open');
        var cutEl = textEl.querySelector('.project-card__desc-cut');
        if (cutEl) cutEl.classList.remove('is-hidden');
      } else {
        fullEl.classList.add('is-open');
        var cutEl = textEl.querySelector('.project-card__desc-cut');
        if (cutEl) cutEl.classList.add('is-hidden');
      }
    }
    return;
  }
  if (full.classList.contains('is-open')) {
    full.classList.remove('is-open');
    cut.classList.remove('is-hidden');
  } else {
    full.classList.add('is-open');
    cut.classList.add('is-hidden');
  }
}

(function () {
  'use strict';
  var buttons = document.querySelectorAll('.projects-filter__btn');
  var categories = document.querySelectorAll('[data-category]');
  var filterBar = document.querySelector('.projects-filter');

  function setFilter(filter) {
    categories.forEach(function (sec) {
      var show = filter === 'all' || sec.getAttribute('data-category') === filter;
      sec.style.display = show ? '' : 'none';
    });
    buttons.forEach(function (btn) {
      var active = btn.getAttribute('data-filter') === filter;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', String(active));
    });
    if (filterBar) {
      if (window.lenis) {
        window.lenis.scrollTo(filterBar, { offset: 0, duration: 1.0 });
      } else {
        filterBar.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  }

  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var filter = btn.getAttribute('data-filter');
      if (btn.classList.contains('is-active')) {
        setFilter('all');
      } else {
        setFilter(filter);
      }
    });
  });
})();
</script>
<?php
require __DIR__ . '/partials/footer.php';
