<?php
/**
 * SERAPH BUILD CONSTRUCTION — Projects page.
 * Separate portfolio page: On Going Projects + Completed Projects.
 */
$site = require __DIR__ . '/config/site.php';

// Central project data — status: 'ongoing' | 'completed'
$projects = [
    // ---- On Going Projects ----
    ['no' => '01', 'status' => 'ongoing', 'category' => 'Luxury Residence', 'title' => 'Villa Seraph', 'text' => '12,000 sq.ft. private residence in Mumbai — crafted around light, view and quiet luxury.',
     'img' => 'images/projects/theme-home-daylight-warm.webp',
     'location' => 'Mumbai', 'plot' => '6,200 sq.ft.', 'builtup' => '12,000 sq.ft.', 'floors' => '4', 'bedrooms' => '5', 'bathrooms' => '6', 'style' => 'Contemporary Luxury'],
    ['no' => '02', 'status' => 'ongoing', 'category' => 'Architecture', 'title' => 'The Glasshouse', 'text' => 'A contemporary family home in Pune where floor-to-ceiling glazing dissolves the line between inside and garden.',
     'img' => 'images/glass_house@1000w.webp',
     'location' => 'Pune', 'plot' => '4,800 sq.ft.', 'builtup' => '8,400 sq.ft.', 'floors' => '3', 'bedrooms' => '4', 'bathrooms' => '4', 'style' => 'Modern Minimal'],
    ['no' => '03', 'status' => 'ongoing', 'category' => 'Hotel', 'title' => 'Hotel Aurelia', 'text' => 'A 65-key boutique hotel in Jaipur — sculpted lobby, guest suites and dining hall in heritage stone, warm wood and layered hospitality lighting.',
     'img' => 'images/hotel_aurelia@1000w.webp',
     'location' => 'Jaipur', 'plot' => '18,000 sq.ft.', 'builtup' => '42,000 sq.ft.', 'floors' => '6', 'bedrooms' => '65', 'bathrooms' => '72', 'style' => 'Heritage Boutique'],
    ['no' => '04', 'status' => 'ongoing', 'category' => 'Interior Design', 'title' => 'Penthouse Noir', 'text' => 'A skyline penthouse in Bengaluru finished in stone, smoked oak and soft ambient light.',
     'img' => 'images/projects/theme-penthouse-light-camel.webp',
     'location' => 'Bengaluru', 'plot' => '—', 'builtup' => '4,200 sq.ft.', 'floors' => '2', 'bedrooms' => '4', 'bathrooms' => '5', 'style' => 'Dark Luxe'],
    ['no' => '05', 'status' => 'ongoing', 'category' => 'Renovation', 'title' => 'Garden Pavilion', 'text' => 'Heritage home restoration in Delhi — original details preserved, modern systems quietly added.',
     'img' => 'images/garden@1000w.webp',
     'location' => 'Delhi', 'plot' => '3,600 sq.ft.', 'builtup' => '6,800 sq.ft.', 'floors' => '3', 'bedrooms' => '4', 'bathrooms' => '4', 'style' => 'Heritage Revival'],
    ['no' => '06', 'status' => 'ongoing', 'category' => 'Commercial', 'title' => 'Skyline Offices', 'text' => 'Corporate headquarters in Noida designed for collaboration, calm and uncompromising build quality.',
     'img' => 'images/projects/theme-offices-silver-blue.webp',
     'location' => 'Noida', 'plot' => '9,400 sq.ft.', 'builtup' => '26,500 sq.ft.', 'floors' => '5', 'bedrooms' => '0', 'bathrooms' => '10', 'style' => 'Corporate Modern'],

    // ---- Completed Projects ----
    ['no' => '07', 'status' => 'completed', 'category' => 'Luxury Residence', 'title' => 'The Aurelia Villa', 'text' => 'A waterfront home in Goa completed on schedule — bespoke interiors, resort-grade landscaping and lifetime durability.',
     'img' => 'images/livingroom@1112w.webp',
     'location' => 'Goa', 'plot' => '7,800 sq.ft.', 'builtup' => '11,200 sq.ft.', 'floors' => '3', 'bedrooms' => '5', 'bathrooms' => '6', 'style' => 'Coastal Luxury'],
    ['no' => '08', 'status' => 'completed', 'category' => 'Interior Design', 'title' => 'Serene Penthouse', 'text' => 'Full-scale interior fit-out in Hyderabad — custom joinery, layered lighting and hand-finished plaster walls.',
     'img' => 'images/bedroom@1112w.webp',
     'location' => 'Hyderabad', 'plot' => '—', 'builtup' => '3,900 sq.ft.', 'floors' => '2', 'bedrooms' => '3', 'bathrooms' => '4', 'style' => 'Warm Contemporary'],
    ['no' => '09', 'status' => 'completed', 'category' => 'Modular Kitchen', 'title' => 'Maison Kitchen', 'text' => 'A modular kitchen crafted with precision-engineered cabinetry, premium appliances and seamless finishes.',
     'img' => 'images/modularkitchen@1112w.webp',
     'location' => 'Chennai', 'plot' => '—', 'builtup' => '450 sq.ft.', 'floors' => '1', 'bedrooms' => '0', 'bathrooms' => '1', 'style' => 'Minimal Kitchen'],
    ['no' => '10', 'status' => 'completed', 'category' => 'Architecture', 'title' => 'Sanctuary Home', 'text' => 'A private residence in Coimbatore delivered as a calm, light-filled sanctuary built to endure.',
     'img' => 'images/interior-living-room@768w.webp',
     'location' => 'Coimbatore', 'plot' => '5,200 sq.ft.', 'builtup' => '7,600 sq.ft.', 'floors' => '3', 'bedrooms' => '4', 'bathrooms' => '5', 'style' => 'Modern Tropical'],
    ['no' => '11', 'status' => 'completed', 'category' => 'Interior Design', 'title' => 'Elevate Residence', 'text' => 'Contemporary duplex interiors in Chennai — stone, steel and warm woods composed in quiet harmony.',
     'img' => 'images/elevation@1112w.webp',
     'location' => 'Chennai', 'plot' => '—', 'builtup' => '3,100 sq.ft.', 'floors' => '2', 'bedrooms' => '3', 'bathrooms' => '3', 'style' => 'Urban Minimal'],
    ['no' => '12', 'status' => 'completed', 'category' => 'Commercial', 'title' => 'Bath & Beyond', 'text' => 'Premium bath and spa fit-out completed flawlessly — waterproofing, tiling and vanity installation.',
     'img' => 'images/toilet@1112w.webp',
     'location' => 'Bengaluru', 'plot' => '—', 'builtup' => '1,100 sq.ft.', 'floors' => '1', 'bedrooms' => '0', 'bathrooms' => '6', 'style' => 'Spa Minimal'],
];

$ongoing  = array_filter($projects, fn($p) => $p['status'] === 'ongoing');
$completed = array_filter($projects, fn($p) => $p['status'] === 'completed');

require __DIR__ . '/partials/header.php';
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
    .projects-page__category-label { display: flex; align-items: center; gap: 1.2rem; margin-bottom: 2.2rem; }
    .projects-page__category-label h2 { font-family: 'Fraunces', Georgia, serif; font-weight: 400; font-size: clamp(1.6rem, 3vw, 2.2rem); color: #f4efe8; }
    .projects-page__category-label span { font-size: 0.85rem; letter-spacing: 0.15em; text-transform: uppercase; color: #C79A56; }
    .projects-page__category-label::after { content: ''; flex: 1; height: 1px; background: #2a261f; }

    .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.1rem; }
    .project-card { position: relative; display: flex; flex-direction: column; overflow: hidden; border-radius: 4px; background: #16140f; text-decoration: none; color: inherit; height: 100%; }
    .project-card__link { display: block; text-decoration: none; color: inherit; }
    .project-card__media { aspect-ratio: 16 / 11; overflow: hidden; }
    .project-card__media img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.2, 0.6, 0.2, 1); }
    .project-card__link:hover .project-card__media img { transform: scale(1.05); }
    .project-card__body { padding: 0.9rem 0.9rem 0.9rem; flex: 1; }
    .project-card__no { font-size: 0.66rem; letter-spacing: 0.18em; text-transform: uppercase; color: #C79A56; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .project-card__title { font-family: 'Fraunces', Georgia, serif; font-size: 1.1rem; font-weight: 400; margin: 0.3rem 0 0.25rem; color: #f4efe8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .project-card__text { font-size: 0.82rem; color: #a29a8c; margin-bottom: 0.7rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.3em; }
    .project-card__arrow { position: absolute; top: 0.8rem; right: 0.8rem; width: 34px; height: 34px; border-radius: 50%; background: #C79A56; color: #141210; display: grid; place-items: center; opacity: 0; transform: translateY(-8px); transition: all 0.3s ease; font-size: 0.85rem; }
    .project-card:hover .project-card__arrow { opacity: 1; transform: translateY(0); }

    .project-card__specs { display: grid; grid-template-columns: 1fr 1fr; gap: 0.45rem 0.8rem; border-top: 1px solid #2a261f; padding-top: 0.7rem; }
    .project-card__spec { display: flex; justify-content: space-between; align-items: baseline; gap: 0.4rem; font-size: 0.72rem; line-height: 1.4; min-height: 1.4em; white-space: nowrap; }
    .project-card__spec-label { color: #8a8377; letter-spacing: 0.04em; flex-shrink: 0; }
    .project-card__spec-value { color: #e9e5de; text-align: right; overflow: hidden; text-overflow: ellipsis; }

    .project-card__download { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1rem; margin: auto 0.9rem 0.9rem; border: 1px solid #3a352c; border-radius: 3px; background: transparent; color: #c9c2b5; font-family: inherit; font-size: 0.78rem; letter-spacing: 0.08em; text-transform: uppercase; text-decoration: none; transition: all 0.3s ease; white-space: nowrap; }
    .project-card__download:hover { border-color: #C79A56; color: #C79A56; background: rgba(199,154,86,0.08); }

    .projects-page__cta { max-width: 1320px; margin: 5rem auto 0; padding: 0 2rem; text-align: center; }
    .btn--gold { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.9rem 2rem; background: #C79A56; color: #141210; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; font-size: 0.85rem; border-radius: 2px; text-decoration: none; transition: background 0.3s ease; }
    .btn--gold:hover { background: #e0b376; }
  </style>

  <div class="projects-page__head">
    <span class="eyebrow">Portfolio</span>
    <h1>Our Projects</h1>
    <p>From private villas to corporate headquarters — explore the work shaping our built environment today, and the projects already delivered.</p>
  </div>

  <div class="projects-filter" role="group" aria-label="Filter projects">
    <button type="button" class="projects-filter__btn" data-filter="ongoing" aria-pressed="false">On Going Projects</button>
    <button type="button" class="projects-filter__btn" data-filter="completed" aria-pressed="false">Completed Projects</button>
  </div>

  <section class="projects-page__category" id="ongoing" data-category="ongoing">
    <div class="projects-page__category-label">
      <h2>On Going Projects</h2>
      <span><?php echo count($ongoing); ?> project(s)</span>
    </div>
    <div class="projects-grid">
      <?php foreach ($ongoing as $p): ?>
        <div class="project-card" title="<?php echo htmlspecialchars($p['title']); ?>">
          <a class="project-card__link" href="#contact">
            <div class="project-card__media"><img src="<?php echo $p['img']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy"></div>
            <div class="project-card__arrow">&#8599;</div>
            <div class="project-card__body">
              <div class="project-card__no"><?php echo $p['no']; ?> &middot; <?php echo htmlspecialchars($p['category']); ?></div>
              <div class="project-card__title"><?php echo htmlspecialchars($p['title']); ?></div>
              <div class="project-card__text"><?php echo $p['text']; ?></div>
              <div class="project-card__specs">
                <div class="project-card__spec"><span class="project-card__spec-label">Location</span><span class="project-card__spec-value"><?php echo $p['location']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Plot Size</span><span class="project-card__spec-value"><?php echo $p['plot']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Built-up Area</span><span class="project-card__spec-value"><?php echo $p['builtup']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Floors</span><span class="project-card__spec-value"><?php echo $p['floors']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Bedrooms</span><span class="project-card__spec-value"><?php echo $p['bedrooms']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Bathrooms</span><span class="project-card__spec-value"><?php echo $p['bathrooms']; ?></span></div>
                <div class="project-card__spec" style="grid-column:1 / -1"><span class="project-card__spec-label">Style</span><span class="project-card__spec-value"><?php echo $p['style']; ?></span></div>
              </div>
            </div>
          </a>
          <a class="project-card__download" href="download-layout.php?p=<?php echo $p['no']; ?>" download><i class="fa-solid fa-download" aria-hidden="true"></i> Download Layout</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="projects-page__category" id="completed" data-category="completed">
    <div class="projects-page__category-label">
      <h2>Completed Projects</h2>
      <span><?php echo count($completed); ?> project(s)</span>
    </div>
    <div class="projects-grid">
      <?php foreach ($completed as $p): ?>
        <div class="project-card" title="<?php echo htmlspecialchars($p['title']); ?>">
          <a class="project-card__link" href="#contact">
            <div class="project-card__media"><img src="<?php echo $p['img']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy"></div>
            <div class="project-card__arrow">&#8599;</div>
            <div class="project-card__body">
              <div class="project-card__no"><?php echo $p['no']; ?> &middot; <?php echo htmlspecialchars($p['category']); ?></div>
              <div class="project-card__title"><?php echo htmlspecialchars($p['title']); ?></div>
              <div class="project-card__text"><?php echo $p['text']; ?></div>
              <div class="project-card__specs">
                <div class="project-card__spec"><span class="project-card__spec-label">Location</span><span class="project-card__spec-value"><?php echo $p['location']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Plot Size</span><span class="project-card__spec-value"><?php echo $p['plot']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Built-up Area</span><span class="project-card__spec-value"><?php echo $p['builtup']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Floors</span><span class="project-card__spec-value"><?php echo $p['floors']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Bedrooms</span><span class="project-card__spec-value"><?php echo $p['bedrooms']; ?></span></div>
                <div class="project-card__spec"><span class="project-card__spec-label">Bathrooms</span><span class="project-card__spec-value"><?php echo $p['bathrooms']; ?></span></div>
                <div class="project-card__spec" style="grid-column:1 / -1"><span class="project-card__spec-label">Style</span><span class="project-card__spec-value"><?php echo $p['style']; ?></span></div>
              </div>
            </div>
          </a>
          <a class="project-card__download" href="download-layout.php?p=<?php echo $p['no']; ?>" download><i class="fa-solid fa-download" aria-hidden="true"></i> Download Layout</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="projects-page__cta">
    <a class="btn--gold" href="#contact">Start Your Project</a>
  </div>
</main>

<script>
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
      var scrollTarget = filterBar;
      if (window.lenis) {
        window.lenis.scrollTo(scrollTarget, { offset: 0, duration: 1.0 });
      } else {
        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  }

  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var filter = btn.getAttribute('data-filter');
      // Clicking the active button again resets to show everything.
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