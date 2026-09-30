<?php
/**
 * SERAPH BUILD CONSTRUCTION — SBC Packages.
 *
 * The public packages page. Two turnkey specifications, followed by the
 * complete line-by-line scope, published in full.
 *
 * All copy and figures come from config/sbc-packages.php, which was
 * transcribed from "images/Sbc Package Details.xlsx". Nothing on this page
 * hard-codes a rate, a count or a clause: change the config and the page
 * changes with it.
 */

declare(strict_types=1);

$site = require __DIR__ . '/config/site.php';
require __DIR__ . '/api/config/bootstrap.php';
require __DIR__ . '/partials/packages/_helpers.php';

$pk      = require __DIR__ . '/config/sbc-packages.php';
$stats   = sk_stats($pk);

$premium = $pk['tiers']['premium'];
$elite   = $pk['tiers']['elite'];
$contact = $site['contact_url'] ?? 'contact.php';
$wa      = $pk['meta']['whatsapp'] ?? 'https://wa.me/919092557722';

$pageMeta = [
    'title'          => 'SBC Packages — Turnkey Homes, Every Line Item Named | SERAPH BUILD CONSTRUCTION',
    'description'    => 'Two turnkey packages at &#8377;' . $premium['rate'] . ' and &#8377;' . $elite['rate']
        . ' per sq.ft. All ' . $stats['rows'] . ' specification line items across ' . $stats['sections']
        . ' sections, published in full — structure, finishes, services and site works.',
    'og_title'       => 'SBC Packages — Turnkey Homes, Every Line Item Named',
    'og_description' => 'Two turnkey packages. ' . $stats['rows'] . ' line items. No ambiguity about what you are paying for.',
    'styles'         => ['css/packages/packages-base.css', 'css/packages/packages-atlas.css'],
];

require __DIR__ . '/partials/header.php';
?>

<main id="main-content" class="sk atl">

  <!-- ============================ HERO ============================ -->
  <header class="atl-hero">
    <div class="sk-wrap">
      <div class="atl-hero__grid">
        <div>
          <span class="sk-kicker">SBC Packages &middot; Turnkey specification</span>
          <h1 class="sk-h1">Build Better.<br>Choose the Right Package.</h1>
          <p class="sk-lede"><?php echo htmlspecialchars($pk['meta']['intro']); ?></p>

          <div class="sk-actions" style="margin-top:2rem">
            <a class="sk-btn sk-btn--primary" href="#pk-spec">
              <i class="fa-solid fa-arrow-down" aria-hidden="true"></i> Read the specification
            </a>
            <a class="sk-btn sk-btn--wa" href="<?php echo htmlspecialchars($wa); ?>" target="_blank" rel="noopener noreferrer">
              <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp us
            </a>
          </div>
        </div>

        <aside class="atl-hero__rate" aria-label="Package rates at a glance">
          <?php foreach ([$premium, $elite] as $tier): ?>
            <div class="atl-rateline">
              <span class="atl-rateline__n"><?php echo htmlspecialchars($tier['short']); ?></span>
              <span class="atl-rateline__v">
                <span class="sk-nowrap">&#8377;<?php echo htmlspecialchars($tier['rate']); ?></span>
                <small><?php echo htmlspecialchars($tier['rate_unit']); ?></small>
              </span>
            </div>
          <?php endforeach; ?>
          <p class="atl-hero__note">
            Rates apply per square foot of carpet area and are confirmed against your
            approved drawings before contract.
          </p>
        </aside>
      </div>

      <div class="sk-stats" style="margin-top:2.75rem">
        <div class="sk-stat"><span class="sk-stat__n"><?php echo $stats['sections']; ?></span><span class="sk-stat__l">Sections</span></div>
        <div class="sk-stat"><span class="sk-stat__n"><?php echo $stats['rows']; ?></span><span class="sk-stat__l">Line items</span></div>
        <div class="sk-stat"><span class="sk-stat__n"><?php echo $stats['diff']; ?></span><span class="sk-stat__l">Upgraded in Elite</span></div>
        <div class="sk-stat"><span class="sk-stat__n"><?php echo $stats['pct']; ?><sup>%</sup></span><span class="sk-stat__l">Differ</span></div>
        <div class="sk-stat"><span class="sk-stat__n">100<sup>%</sup></span><span class="sk-stat__l">Published</span></div>
      </div>
    </div>
  </header>

  <!-- =========================== TIERS ============================ -->
  <section class="sk-section sk-section--alt" aria-labelledby="pk-tiers-h">
    <div class="sk-wrap">
      <div class="sk-center">
        <span class="sk-kicker sk-kicker--plain">Choose your build</span>
        <h2 class="sk-h2" id="pk-tiers-h">Same engineers. Same structure. A harder specification.</h2>
        <p class="sk-lede">Elite is Premium with a decisive uplift across every trade. Where the two
          packages are identical, the specification says so rather than dressing it up.</p>
      </div>

      <div class="atl-tiers">
        <?php foreach ([$premium, $elite] as $i => $tier): ?>
          <?php $featured = ($i === 1); ?>
          <article class="sk-card atl-tier<?php echo $featured ? ' sk-card--feature' : ''; ?>" id="tier-<?php echo htmlspecialchars($tier['key']); ?>">
            <?php if ($tier['badge']): ?>
              <span class="atl-tier__badge sk-pill <?php echo $featured ? 'sk-pill--solid' : 'sk-pill--gold'; ?>">
                <i class="fa-solid <?php echo htmlspecialchars($tier['icon']); ?>" aria-hidden="true"></i>
                <?php echo htmlspecialchars($tier['badge']); ?>
              </span>
            <?php endif; ?>

            <h3 class="atl-tier__name"><?php echo htmlspecialchars($tier['name']); ?></h3>
            <p class="atl-tier__tag"><?php echo htmlspecialchars($tier['tagline']); ?></p>

            <p class="atl-tier__rate">
              <span class="atl-tier__cur">&#8377;</span><span class="atl-tier__num"><?php echo htmlspecialchars($tier['rate']); ?></span>
              <span class="atl-tier__unit">/ <?php echo htmlspecialchars($tier['rate_unit']); ?></span>
            </p>

            <p class="sk-p sk-lede--sm"><?php echo htmlspecialchars($tier['summary']); ?></p>

            <hr class="atl-tier__hr">

            <p class="atl-tier__hl-h">Headline specification</p>
            <ul class="sk-ticks">
              <?php foreach ($tier['highlights'] as $h): ?>
                <li><span><?php echo $h; ?></span></li>
              <?php endforeach; ?>
            </ul>

            <div class="atl-tier__cta">
              <a class="sk-btn <?php echo $featured ? 'sk-btn--primary' : 'sk-btn--ghost'; ?> sk-btn--block" href="<?php echo htmlspecialchars($contact); ?>">
                Enquire about <?php echo htmlspecialchars($tier['short']); ?>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
              </a>
              <a class="sk-btn sk-btn--soft sk-btn--sm sk-btn--block" href="#pk-spec" data-pk-jump="<?php echo htmlspecialchars($tier['key']); ?>">
                See where the two differ
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <p class="atl-tiers__foot">
        <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
        <span><strong><?php echo $stats['same']; ?> of <?php echo $stats['rows']; ?> line items are identical</strong>
        in both packages. The <?php echo $stats['diff']; ?> that differ are marked
        <span class="sk-pill sk-pill--diff">Upgraded</span> throughout the specification below.</span>
      </p>
    </div>
  </section>

  <!-- ========================== SPECIFICATION ====================== -->
  <section class="sk-section" id="pk-spec" aria-labelledby="pk-spec-h">
    <div class="sk-wrap--wide sk-wrap">
      <span class="sk-kicker">The specification</span>
      <h2 class="sk-h2" id="pk-spec-h">All <?php echo $stats['rows']; ?> line items, <?php echo $stats['sections']; ?> sections</h2>
      <p class="sk-lede">This is the actual scope our site teams are contractually required to build — not
        a summary of it. Search it, filter it, print it.</p>
    </div>

    <!-- Sticky toolbar. The sticky element is a direct child of the
         section, otherwise it has no travel inside its own wrapper. -->
    <div class="sk-stickybar">
      <div class="sk-wrap--wide sk-wrap">
        <div class="sk-toolbar">
          <div class="sk-search">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <label class="visually-hidden" for="pk-q">Search the specification</label>
            <input type="search" id="pk-q" data-sk-filter placeholder="Search teak, waterproofing, Legrand&hellip;  press /" autocomplete="off" spellcheck="false">
            <button type="button" class="sk-search__clear" data-sk-filter-reset aria-label="Clear search and filters">
              <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
          </div>

          <label class="sk-switch">
            <input type="checkbox" data-sk-filter-diff>
            <span class="sk-switch__track" aria-hidden="true"><i></i></span>
            <span class="sk-switch__lab">Upgrades only</span>
          </label>

          <span class="sk-toolbar__spacer"></span>
          <span class="sk-count" data-sk-filter-count data-sk-count-word="line items" role="status" aria-live="polite">Showing all <?php echo $stats['rows']; ?> line items</span>
        </div>
      </div>
    </div>

    <div class="sk-wrap--wide sk-wrap atl-body" data-sk-filter-scope style="margin-top:1.5rem">

      <!-- Contents rail -->
      <nav class="sk-rail atl-rail" aria-label="Specification sections">
        <p class="sk-rail__h">Contents</p>
        <?php foreach ($pk['sections'] as $section): ?>
          <a class="sk-rail__a" href="#pk-sec-<?php echo htmlspecialchars($section['id']); ?>" data-sk-rail-link>
            <i><?php echo htmlspecialchars($section['num']); ?></i>
            <span><?php echo strip_tags($section['title']); ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <!-- Sections -->
      <div class="atl-secs">
        <?php foreach ($pk['sections'] as $section): ?>
          <?php
          $secDiff = 0;
          foreach ($section['rows'] as $r) {
              if (trim($r[1]) !== trim($r[2])) { $secDiff++; }
          }
          ?>
          <section class="atl-sec sk-card sk-card--flush"
                   id="pk-sec-<?php echo htmlspecialchars($section['id']); ?>"
                   data-sk-group="<?php echo htmlspecialchars($section['id']); ?>"
                   aria-labelledby="pk-h-<?php echo htmlspecialchars($section['id']); ?>">
            <header class="atl-sec__head">
              <span class="atl-sec__num"><?php echo htmlspecialchars($section['num']); ?></span>
              <div>
                <h3 class="sk-h3" id="pk-h-<?php echo htmlspecialchars($section['id']); ?>">
                  <i class="fa-solid <?php echo htmlspecialchars($section['icon']); ?>" aria-hidden="true"></i>
                  <?php echo $section['title']; ?>
                </h3>
                <p class="atl-sec__blurb"><?php echo htmlspecialchars($section['blurb']); ?></p>
              </div>
              <span class="atl-sec__meta">
                <?php echo count($section['rows']); ?> items
                <?php if ($secDiff): ?>
                  &middot; <b><?php echo $secDiff; ?> upgraded</b>
                <?php endif; ?>
              </span>
            </header>

            <table class="sk-spec sk-spec--bare">
              <caption class="visually-hidden"><?php echo strip_tags($section['title']); ?> — Premium versus Elite</caption>
              <thead>
                <tr>
                  <th scope="col">Specification item</th>
                  <th scope="col"><?php echo htmlspecialchars($premium['short']); ?><small>&#8377;<?php echo htmlspecialchars($premium['rate']); ?>/sq.ft</small></th>
                  <th scope="col"><?php echo htmlspecialchars($elite['short']); ?><small>&#8377;<?php echo htmlspecialchars($elite['rate']); ?>/sq.ft</small></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($section['rows'] as $ri => $r): ?>
                  <?php
                  $same = trim($r[1]) === trim($r[2]);
                  $ref  = sk_ref($section['num'], $ri);
                  ?>
                  <tr class="<?php echo $same ? 'is-same' : 'is-diff'; ?>"
                      data-sk-item
                      data-sk-group-key="<?php echo htmlspecialchars($section['id']); ?>"
                      data-sk-flag="<?php echo $same ? 'same' : 'diff'; ?>"
                      data-sk-text="<?php echo htmlspecialchars(sk_text($r[0] . ' ' . $r[1] . ' ' . $r[2] . ' ' . $section['title'])); ?>">
                    <th scope="row">
                      <span class="sk-spec__ref"><?php echo htmlspecialchars($ref); ?></span>
                      <?php echo $r[0]; ?>
                    </th>
                    <td data-l="<?php echo htmlspecialchars($premium['short']); ?>"><?php echo $r[1]; ?></td>
                    <td data-l="<?php echo htmlspecialchars($elite['short']); ?>"><?php echo $r[2]; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </section>
        <?php endforeach; ?>

        <div class="sk-empty" data-sk-filter-empty>
          <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
          <p><strong>Nothing matches that.</strong></p>
          <p>Try a single word &mdash; <em>teak</em>, <em>tile</em>, <em>wire</em>, <em>waterproof</em>.</p>
          <button type="button" class="sk-btn sk-btn--soft sk-btn--sm" data-sk-filter-reset>Reset filters</button>
        </div>
      </div>
    </div>

    <div class="sk-wrap--wide sk-wrap">
      <div class="sk-note" style="margin-top:2rem">
        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
        <p><strong>Scope note.</strong> <?php echo htmlspecialchars($pk['meta']['disclaimer']); ?></p>
      </div>
    </div>
  </section>

  <!-- ============================== CTA ============================ -->
  <section class="sk-section sk-section--tight">
    <div class="sk-wrap">
      <div class="sk-cta">
        <div class="sk-cta__in">
          <span class="sk-kicker sk-kicker--plain">Next step</span>
          <h2 class="sk-h2">Send us the plan. We will price it line by line.</h2>
          <p class="sk-lede">No obligation, no site visit required to get a first number. Share your
            carpet area and floor plan and we will come back with a line-by-line bill of quantities against
            either specification.</p>
          <div class="sk-actions sk-actions--center" style="margin-top:1.75rem">
            <a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo htmlspecialchars($contact); ?>">
              <i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> Request a quote
            </a>
            <a class="sk-btn sk-btn--ghost sk-btn--lg" href="tel:<?php echo htmlspecialchars($site['phone_tel']); ?>">
              <i class="fa-solid fa-phone" aria-hidden="true"></i> <?php echo htmlspecialchars($site['phone']); ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
/* "See where the two differ" jumps to the first section where the two
   packages actually diverge, so the button is never a dead end.

   The "differs" marker lives on the rows, not the section, so the
   target has to be derived from the first differing row — otherwise
   this lands on section I, which is not guaranteed to differ at all.

   stopPropagation matters: the site-wide handler in js/smooth-scroll.js
   also claims this link and would scroll to the button's own href,
   overriding this one. scrollIntoView then applies the section's
   scroll-margin-top, which clears the sticky toolbar. */
(function () {
  var btn = document.querySelector('[data-pk-jump]');
  if (!btn) { return; }

  var row  = document.querySelector('tr.is-diff');
  var first = row ? row.closest('.atl-sec') : document.querySelector('.atl-sec');
  if (!first) { return; }

  btn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    first.scrollIntoView({ behavior: 'smooth', block: 'start' });
    if (row) {
      row.classList.add('atl-flash');
      setTimeout(function () { row.classList.remove('atl-flash'); }, 1600);
    }
  });
})();

/* The two tier cards must be exactly the same size.

   Side by side the grid alone does it (align-items: stretch). But the grid
   collapses to a single column below 860px, where each card falls back to
   its own content height — and Elite's copy runs longer than Premium's, so
   the pair drifts apart by 20-50px. CSS has no way to equalise siblings
   that each sit in their own grid row, so do it here.

   min-height, not height: the shorter card grows, the taller one is left
   at its natural size, and nothing can ever be clipped.

   Guarded on width, because width is the only thing that changes the line
   counts. That also keeps it clear of a ResizeObserver feedback loop —
   equalising changes the page height, never the grid's width. */
(function () {
  var grid = document.querySelector('.atl-tiers');
  if (!grid) { return; }

  var tiers = grid.querySelectorAll('.atl-tier');
  if (tiers.length < 2) { return; }

  var lastW = -1;

  function equalise() {
    var w = grid.clientWidth;
    if (w === lastW) { return; }
    lastW = w;

    var heights = [];
    for (var i = 0; i < tiers.length; i++) {
      tiers[i].style.minHeight = '';
      heights.push(tiers[i].getBoundingClientRect().height);
    }

    var tallest = Math.ceil(Math.max.apply(null, heights));
    for (var j = 0; j < tiers.length; j++) {
      tiers[j].style.minHeight = tallest + 'px';
    }
  }

  equalise();

  /* Web fonts land after first paint and change every line count, so the
     first measurement is a guess until the real faces are in. */
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(equalise);
  }

  if (window.ResizeObserver) {
    new ResizeObserver(equalise).observe(grid);
  } else {
    window.addEventListener('resize', equalise);
  }
})();
</script>
<?php require __DIR__ . '/partials/packages/_js.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
