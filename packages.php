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
    'styles'         => [
        'css/packages/packages-base.css',
        'css/packages/packages-atlas.css',
        'css/packages/packages-responsive.css',
    ],
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

          <div class="sk-actions atl-hero__actions">
            <a class="sk-btn sk-btn--primary" href="#pk-spec">
              <i class="fa-solid fa-arrow-down" aria-hidden="true"></i> Read the specification
            </a>
            <a class="sk-btn sk-btn--wa" href="<?php echo htmlspecialchars($wa); ?>" target="_blank" rel="noopener noreferrer">
              <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp us
            </a>
            <button type="button" class="sk-btn sk-btn--ghost" id="calcOpen">
              <i class="fa-solid fa-calculator" aria-hidden="true"></i> Cost Calculator
            </button>
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

      <div class="sk-stats atl-hero__stats">
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

    <div class="sk-wrap--wide sk-wrap atl-body" data-sk-filter-scope>

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

            <!-- role=region + tabindex makes the horizontal scroller reachable
                 from the keyboard, which is the only way to reach the Elite
                 column once the table is wider than a phone. -->
            <div class="sk-tablewrap" role="region" tabindex="0"
                 aria-label="<?php echo strip_tags($section['title']); ?> — <?php echo htmlspecialchars($premium['short']); ?> versus <?php echo htmlspecialchars($elite['short']); ?>, scrollable table">
            <table class="sk-spec sk-spec--bare">
              <caption class="visually-hidden"><?php echo strip_tags($section['title']); ?> — Premium versus Elite</caption>
              <thead>
                <tr>
                  <?php /* Soft hyphen inside "Specification": the row
                        heading column is deliberately narrow on phones,
                        and without a break opportunity the browser falls
                        back to overflow-wrap and slices the word as
                        "SPECIFICATI / ON". &shy; gives it a legal break
                        ("SPECIFI-CATION") at any width, with no
                        hyphenation dictionary required and no change to
                        the accessible name. */ ?>
                  <th scope="col">Specifi&shy;cation item</th>
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
            </div>
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
      <div class="sk-note sk-note--spaced">
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
          <div class="sk-actions sk-actions--center sk-cta__actions">
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

   Skipped below 861px, where the grid collapses to one column. Below that
   each card sits in its own grid row, so a shared min-height would pad
   the shorter card out with a large empty block rather than aligning
   anything. */
(function () {
  var grid = document.querySelector('.atl-tiers');
  if (!grid) { return; }

  var stackQuery = window.matchMedia('(max-width: 860px)');
  var tiers = grid.querySelectorAll('.atl-tier');
  if (tiers.length < 2) { return; }

  var lastW = -1;

  function equalise() {
    /* One column: clear anything a previous wide layout applied. */
    if (stackQuery.matches) {
      for (var k = 0; k < tiers.length; k++) { tiers[k].style.minHeight = ''; }
      return;
    }

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

  /* Crossing the breakpoint changes the answer, and the observer only
     fires on a width change, so the query itself has to be watched. */
  if (stackQuery.addEventListener) {
    stackQuery.addEventListener('change', equalise);
  } else if (stackQuery.addListener) {
    stackQuery.addListener(equalise);
  }

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
<button type="button" id="calcFab" class="calc-fab sk-scope" aria-label="Open cost calculator">
  <i class="fa-solid fa-calculator" aria-hidden="true"></i>
</button>

<!-- ======================= COST CALCULATOR POPUP ======================= -->
<?php /* data-lenis-prevent(-wheel/-touch) are Lenis's own opt-outs.

           Lenis owns wheel and touch events across the whole document.
           Inside a modal that is fatal: it consumes the gesture, and the
           sheet's scroll region — the only way to reach the lower
           inputs — never moves. Marking the dialog tells Lenis to leave
           scrolling inside it alone. Without these the calculator is
           unusable on the live site. */ ?>
<div class="calc-pop sk-scope" id="calcPop" role="dialog" aria-modal="true" aria-labelledby="calcTitle"
     data-lenis-prevent data-lenis-prevent-wheel data-lenis-prevent-touch hidden>
  <div class="calc-pop__backdrop" data-calc-close></div>
  <div class="calc-pop__box" role="document">
    <button type="button" class="calc-pop__x" data-calc-close aria-label="Close calculator">&times;</button>
    <h2 class="sk-h2" id="calcTitle">Home Construction Cost Calculator</h2>
    <p class="sk-lede sk-lede--sm">You can arrive at your construction estimate here</p>

    <?php /* The two dropdowns are custom controls, not <select> elements.

           A native select's option list is drawn by the OS — Android,
           iOS and Windows each render it with their own font size,
           padding and popup width, and none of it is reachable from CSS.
           That is why the option text could not be made to match the
           rest of the sheet: the rate inside the list was sized by the
           platform, not by this page.

           The hidden input keeps the id, the value and the change event
           that the calculator script below already reads, so nothing
           downstream had to change. The button + listbox pair is the
           ARIA combobox pattern and is styled and sized like every other
           part of the sheet. */ ?>
    <div class="calc-pop__controls">
      <div class="calc-pop__field">
        <span class="calc-pop__fieldlab" id="calcFloorsLab">No. of Floors</span>
        <div class="calc-dd" data-calc-dd>
          <input type="hidden" id="calcFloors" value="1">
          <button type="button" class="calc-pop__sel" role="combobox" aria-expanded="false"
                  aria-haspopup="listbox" aria-labelledby="calcFloorsLab">
            <span class="calc-dd__val">Ground</span>
            <i class="fa-solid fa-chevron-down calc-dd__chev" aria-hidden="true"></i>
          </button>
          <ul class="calc-dd__list" role="listbox" aria-labelledby="calcFloorsLab" hidden>
            <li role="option" aria-selected="true"  data-v="1" tabindex="-1">Ground</li>
            <li role="option" aria-selected="false" data-v="2" tabindex="-1">G + 1</li>
            <li role="option" aria-selected="false" data-v="3" tabindex="-1">G + 2</li>
            <li role="option" aria-selected="false" data-v="4" tabindex="-1">G + 3</li>
            <li role="option" aria-selected="false" data-v="5" tabindex="-1">G + 4</li>
            <li role="option" aria-selected="false" data-v="6" tabindex="-1">G + 5</li>
          </ul>
        </div>
      </div>

      <div class="calc-pop__field">
        <span class="calc-pop__fieldlab" id="calcPkgLab">Package</span>
        <div class="calc-dd" data-calc-dd>
          <input type="hidden" id="calcPkg" value="2300">
          <button type="button" class="calc-pop__sel" role="combobox" aria-expanded="false"
                  aria-haspopup="listbox" aria-labelledby="calcPkgLab">
            <span class="calc-dd__val">Premium Package @ &#8377;2,300/sqft</span>
            <i class="fa-solid fa-chevron-down calc-dd__chev" aria-hidden="true"></i>
          </button>
          <ul class="calc-dd__list" role="listbox" aria-labelledby="calcPkgLab" hidden>
            <li role="option" aria-selected="true"  data-v="2300" tabindex="-1">Premium Package @ &#8377;2,300/sqft</li>
            <li role="option" aria-selected="false" data-v="2900" tabindex="-1">Elite Package @ &#8377;2,900/sqft</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="calc-pop__tablewrap">
      <table class="calc-pop__tbl">
        <thead>
          <tr>
            <th scope="col">Work</th>
            <th scope="col">Area</th>
            <th scope="col">Unit</th>
            <th scope="col">Rate</th>
            <th scope="col">Cost</th>
          </tr>
        </thead>
        <tbody>
          <?php /* The work cell is a <th scope="row"> and every value cell
                 carries data-l. Both exist for the stacked phone layout
                 in css/packages/packages-responsive.css: the row header
                 gives a screen reader the row's subject, and data-l is
                 what the card layout prints as the field label once the
                 five columns stop being columns. On desktop neither is
                 visible. */ ?>
          <?php $floorNames = ['Ground Floor', 'First Floor', 'Second Floor', 'Third Floor', 'Fourth Floor', 'Fifth Floor']; ?>
          <?php for ($i = 1; $i <= 6; $i++): ?>
          <tr class="calc-pop__row calc-floor" data-floor="<?php echo $i; ?>"<?php echo $i > 1 ? ' hidden' : ''; ?>>
            <th scope="row" class="calc-pop__work" data-l="Work">Built-up area &mdash; <?php echo $floorNames[$i - 1]; ?></th>
            <td data-l="Area">
              <input type="number" min="0" step="1" inputmode="decimal" id="calcCost<?php echo $i; ?>" class="calc-pop__inp" placeholder="Area in sqft"
                     aria-label="<?php echo $floorNames[$i - 1]; ?> built-up area in square feet">
            </td>
            <td data-l="Unit">sqft</td>
            <td data-l="Rate">&#8377;<span class="calc-pkg-rate">2300</span></td>
            <td data-l="Cost">&#8377; <span id="calcPrice<?php echo $i; ?>">0</span></td>
          </tr>
          <?php endfor; ?>
          <tr class="calc-pop__row">
            <th scope="row" class="calc-pop__work" data-l="Work">Size of RCC Water Sump <small>(a 4 member family requires 9000 ltr)</small></th>
            <td data-l="Area">
              <input type="number" min="0" step="1" inputmode="decimal" id="calcSump" class="calc-pop__inp" placeholder="No. of Litres" aria-label="RCC water sump capacity in litres">
            </td>
            <td data-l="Unit">ltr</td>
            <td data-l="Rate">&#8377;30</td>
            <td data-l="Cost">&#8377; <span id="calcSumpPrice">0</span></td>
          </tr>
          <tr class="calc-pop__row">
            <th scope="row" class="calc-pop__work" data-l="Work">Size of Septic Tank</th>
            <td data-l="Area">
              <input type="number" min="0" step="1" inputmode="decimal" id="calcSeptic" class="calc-pop__inp" placeholder="No. of Litres" aria-label="Septic tank capacity in litres">
            </td>
            <td data-l="Unit">ltr</td>
            <td data-l="Rate">&#8377;30</td>
            <td data-l="Cost">&#8377; <span id="calcSepticPrice">0</span></td>
          </tr>
          <tr class="calc-pop__row">
            <th scope="row" class="calc-pop__work" data-l="Work">Plain Compound Wall</th>
            <td class="calc-pop__pair" data-l="Area">
              <label class="calc-pop__mini">
                <span class="calc-pop__minilab">Length</span>
                <input type="number" min="0" step="1" inputmode="decimal" id="calcWallL" class="calc-pop__inp" placeholder="in ft">
              </label>
              <label class="calc-pop__mini">
                <span class="calc-pop__minilab">Height</span>
                <input type="number" min="0" step="1" inputmode="decimal" id="calcWallH" class="calc-pop__inp" placeholder="in ft">
              </label>
            </td>
            <td data-l="Unit">sqft</td>
            <td data-l="Rate">&#8377;425</td>
            <td data-l="Cost">&#8377; <span id="calcWallPrice">0</span></td>
          </tr>
          <tr class="calc-pop__total">
            <td colspan="4" data-l="Total"><b>Total Construction Cost</b></td>
            <td data-l="Cost"><b>&#8377; <span id="calcTotal">0</span></b></td>
          </tr>
        </tbody>
      </table>
    </div>

    <form class="sk-actions" id="calcEstimateForm" method="POST" action="/contact">
      <?php echo CSRF::field(); ?>
      <input type="hidden" name="calc_estimate" value="1">
      <input type="hidden" name="calc_floors" id="hCalcFloors" value="1">
      <input type="hidden" name="calc_package" id="hCalcPackage" value="premium">
      <?php for ($i = 1; $i <= 6; $i++): ?>
        <input type="hidden" name="calc_area_<?php echo $i; ?>" id="hCalcArea<?php echo $i; ?>" value="0">
      <?php endfor; ?>
      <input type="hidden" name="calc_sump" id="hCalcSump" value="0">
      <input type="hidden" name="calc_septic" id="hCalcSeptic" value="0">
      <input type="hidden" name="calc_wall_l" id="hCalcWallL" value="0">
      <input type="hidden" name="calc_wall_h" id="hCalcWallH" value="0">
      <button type="submit" class="sk-btn sk-btn--primary">
        <i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> Get Free Estimate Now
      </button>
    </form>
  </div>
</div>

<script>
(function () {
  var pop = document.getElementById('calcPop');
  if (!pop) { return; }

  var box = pop.querySelector('.calc-pop__box');
  var lastFocus = null;

  /* iOS ignores overflow:hidden on <body> — the page behind the sheet
     still scrolls under your thumb. Pinning the body and restoring its
     scroll offset on close is what actually holds it still. */
  var lockY = 0;
  function lock() {
    lockY = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = '-' + lockY + 'px';
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
    document.body.style.overflow = 'hidden';
  }
  function unlock() {
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';
    document.body.style.overflow = '';
    window.scrollTo(0, lockY);
  }

  function open(trigger) {
    if (!pop.hidden) { return; }
    lastFocus = trigger || document.activeElement;
    pop.hidden = false;
    lock();
    if (window.lenis) { window.lenis.stop(); }
    /* Move focus into the dialog, or a screen reader and a keyboard user
       are both still on the page behind it. */
    var first = pop.querySelector('.calc-pop__x') || box;
    if (first) { first.focus({ preventScroll: true }); }
  }

  function close() {
    if (pop.hidden) { return; }
    pop.hidden = true;
    unlock();
    if (window.lenis) { window.lenis.start(); }
    if (lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
    lastFocus = null;
  }

  document.getElementById('calcOpen').addEventListener('click', function () { open(this); });

  /* The floating action button lives in a separate script below and has
     to open this same dialog — with the same scroll lock, focus move
     and focus return — rather than just flipping `hidden`. */
  window.openCostCalculator = open;

  pop.querySelectorAll('[data-calc-close]').forEach(function (el) {
    el.addEventListener('click', close);
  });

  document.addEventListener('keydown', function (e) {
    if (pop.hidden) { return; }
    if (e.key === 'Escape') { close(); return; }
    /* Trap Tab inside the dialog. Without this, Tab walks off the end of
       the sheet and into the page behind it, which is still visible on
       landscape phones where the modal is not full-screen. */
    if (e.key !== 'Tab') { return; }
    var focusables = box.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );
    if (!focusables.length) { return; }
    var firstEl = focusables[0];
    var lastEl = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === firstEl) {
      e.preventDefault(); lastEl.focus();
    } else if (!e.shiftKey && document.activeElement === lastEl) {
      e.preventDefault(); firstEl.focus();
    }
  });

  /* ---------- Custom dropdowns ----------
     Each one is a hidden input (the value), a combobox button (the
     closed state) and a listbox (the options). Selecting fires a real
     `change` on the input, so the handlers further down are unchanged.

     The listbox is not portalled and not absolutely positioned against
     the viewport: it is a plain block inside the dialog's own flow.
     That is deliberate — it means the options scroll with the sheet
     like any other content, cannot be clipped by an overflow ancestor,
     and can never be left hanging off the bottom of the screen on a
     short phone. */
  var openList = null;

  function closeDD() {
    if (!openList) { return; }
    var dd = openList.closest('[data-calc-dd]');
    openList.hidden = true;
    dd.querySelector('[role="combobox"]').setAttribute('aria-expanded', 'false');
    openList = null;
  }

  pop.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && openList) {
      e.stopPropagation();
      closeDD();
      openList = null;
    }
  });

  document.addEventListener('click', function (e) {
    if (openList && !e.target.closest('[data-calc-dd]')) { closeDD(); }
  });

  pop.querySelectorAll('[data-calc-dd]').forEach(function (dd) {
    var input = dd.querySelector('input[type="hidden"]');
    var btn = dd.querySelector('[role="combobox"]');
    var list = dd.querySelector('[role="listbox"]');
    var val = dd.querySelector('.calc-dd__val');
    var opts = Array.prototype.slice.call(list.querySelectorAll('[role="option"]'));

    function select(o) {
      opts.forEach(function (x) { x.setAttribute('aria-selected', String(x === o)); });
      input.value = o.getAttribute('data-v');
      val.textContent = o.textContent.trim();
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function openDD() {
      closeDD();
      list.hidden = false;
      btn.setAttribute('aria-expanded', 'true');
      openList = list;
    }

    function move(step) {
      var i = opts.indexOf(opts.filter(function (o) {
        return o.getAttribute('aria-selected') === 'true';
      })[0]);
      var next = opts[(i + step + opts.length) % opts.length];
      select(next);
    }

    btn.addEventListener('click', function () {
      if (list.hidden) { openDD(); } else { closeDD(); }
    });

    /* The listbox is walked with the arrow keys, but only while it is
       open — otherwise the calculator's own Tab trap would fight it. */
    dd.addEventListener('keydown', function (e) {
      if (list.hidden) {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
          e.preventDefault();
          openDD();
          move(e.key === 'ArrowDown' ? 1 : -1);
        }
        return;
      }
      if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
      else if (e.key === 'Home') { e.preventDefault(); select(opts[0]); }
      else if (e.key === 'End') { e.preventDefault(); select(opts[opts.length - 1]); }
    });

    opts.forEach(function (o) {
      o.addEventListener('click', function () {
        select(o);
        closeDD();
        btn.focus();
      });
    });

    /* Opening must not also trip the outside-click handler that is
       about to run for this same click. */
    list.addEventListener('click', function (e) { e.stopPropagation(); });
  });

  var floorsSel = document.getElementById('calcFloors');
  var pkgSel = document.getElementById('calcPkg');

  function fmt(n) {
    n = String(Math.round(n) || 0);
    var last3 = n.slice(-3), rest = n.slice(0, -3);
    if (rest !== '') { last3 = ',' + last3; }
    return rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + last3;
  }

  function num(id) {
    var v = parseFloat(document.getElementById(id).value);
    return isNaN(v) || v < 0 ? 0 : v;
  }

  function setFloors() {
    var n = parseInt(floorsSel.value, 10);
    pop.querySelectorAll('tr.calc-floor').forEach(function (tr) {
      tr.hidden = parseInt(tr.getAttribute('data-floor'), 10) > n;
    });
    calc();
  }

  function setPkg() {
    var rate = pkgSel.value;
    pop.querySelectorAll('.calc-pkg-rate').forEach(function (s) { s.textContent = rate; });
    calc();
  }

  function calc() {
    var rate = parseFloat(pkgSel.value) || 0;
    var nFloors = parseInt(floorsSel.value, 10) || 1;
    var total = 0;
    for (var i = 1; i <= 6; i++) {
      var p = i <= nFloors ? Math.round(num('calcCost' + i) * rate) : 0;
      document.getElementById('calcPrice' + i).textContent = fmt(p);
      total += p;
    }
    var sump = Math.round(num('calcSump') * 30);
    var septic = Math.round(num('calcSeptic') * 30);
    var wall = Math.round(num('calcWallL') * num('calcWallH') * 425);
    document.getElementById('calcSumpPrice').textContent = fmt(sump);
    document.getElementById('calcSepticPrice').textContent = fmt(septic);
    document.getElementById('calcWallPrice').textContent = fmt(wall);
    total += sump + septic + wall;
    document.getElementById('calcTotal').textContent = fmt(total);
  }

  floorsSel.addEventListener('change', setFloors);
  pkgSel.addEventListener('change', setPkg);
  pop.querySelectorAll('.calc-pop__inp').forEach(function (inp) {
    inp.addEventListener('input', calc);
  });
  setFloors();

  document.getElementById('calcEstimateForm').addEventListener('submit', function () {
    document.getElementById('hCalcFloors').value = floorsSel.value;
    document.getElementById('hCalcPackage').value = pkgSel.value === '2900' ? 'elite' : 'premium';
    for (var i = 1; i <= 6; i++) {
      document.getElementById('hCalcArea' + i).value = i <= parseInt(floorsSel.value, 10) ? num('calcCost' + i) : 0;
    }
    document.getElementById('hCalcSump').value = num('calcSump');
    document.getElementById('hCalcSeptic').value = num('calcSeptic');
    document.getElementById('hCalcWallL').value = num('calcWallL');
    document.getElementById('hCalcWallH').value = num('calcWallH');
  });
})();

/* Floating calculator button (bottom-right).

   Lives after the modal markup on purpose: it needs #calcPop to exist at
   bind time, and an earlier placement silently returned early — the button
   then kept `visibility: hidden` forever and appeared to do nothing.

   Scroll position is read from Lenis when it is present, but the listener
   is attached to the native scroll event either way: this script runs
   before js/smooth-scroll.js (both are deferred/inline at parse time), so
   window.lenis does not exist yet, and Lenis drives real window scroll,
   which does fire native scroll events. */
(function () {
  var fab = document.getElementById('calcFab');
  var pop = document.getElementById('calcPop');
  if (!fab || !pop) { return; }

  function onScroll() {
    var y = window.lenis ? window.lenis.scroll : (window.scrollY || window.pageYOffset);
    fab.classList.toggle('is-visible', y > 120);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  onScroll();

  /* The button is chrome over the page, never over the dialog. */
  var fabObserve = function () {
    fab.classList.toggle('is-hidden', !pop.hidden);
  };
  new MutationObserver(fabObserve).observe(pop, { attributes: true, attributeFilter: ['hidden'] });
  fabObserve();

  fab.addEventListener('click', function () {
    if (window.openCostCalculator) {
      window.openCostCalculator(this);
    } else {
      pop.hidden = false;
      document.body.style.overflow = 'hidden';
      if (window.lenis) { window.lenis.stop(); }
    }
    onScroll();
  });
})();
</script>

<?php require __DIR__ . '/partials/packages/_js.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
