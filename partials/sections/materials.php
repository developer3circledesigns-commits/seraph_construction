<!-- =====================================================
     4. MATERIALS — Layout 4 pinned horizontal scroll
     ===================================================== -->
<?php
$filters = [
    ['key' => 'all',       'icon' => 'fa-border-all', 'label' => 'All'],
    ['key' => 'tiles',     'icon' => 'fa-th-large',   'label' => 'Tiles'],
    ['key' => 'steel',     'icon' => 'fa-industry',   'label' => 'Steel'],
    ['key' => 'doors',     'icon' => 'fa-door-open',  'label' => 'Doors &amp; Fittings'],
    ['key' => 'wood',      'icon' => 'fa-tree',       'label' => 'Wood'],
    ['key' => 'electrical','icon' => 'fa-bolt',       'label' => 'Electrical'],
    ['key' => 'paints',    'icon' => 'fa-paint-roller','label' => 'Paints'],
    ['key' => 'plumbing',  'icon' => 'fa-faucet',     'label' => 'Plumbing'],
    ['key' => 'bath',      'icon' => 'fa-bath',       'label' => 'Bath Fittings'],
    ['key' => 'switches',  'icon' => 'fa-toggle-on',  'label' => 'Switches'],
];

/* Card srcset capped at display size; bg_src capped at 1024px for full-bleed overlay. */
$materials = [
    ['category' => 'tiles',  'img' => 'materials/kajaria.png', 'srcset' => 'images/materials/kajaria.png 1112w', 'bg_src' => 'images/materials/kajaria.png', 'alt' => 'Premium Italian marble tiles', 'name' => 'Kajaria', 'type' => 'Premium Tiles', 'specs' => ['Italian Finish', 'Water Resistant', 'Scratch Proof']],
    ['category' => 'steel',  'img' => 'materials/tata_steel.png', 'srcset' => 'images/materials/tata_steel.png 1112w', 'bg_src' => 'images/materials/tata_steel.png', 'alt' => 'Structural steel beams for construction', 'name' => 'Tata Steel', 'type' => 'Structural Steel', 'specs' => ['High Tensile', 'Corrosion Resistant', 'ISI Certified']],
    ['category' => 'doors',  'img' => 'materials/greenply.png', 'srcset' => 'images/materials/greenply.png 1112w', 'bg_src' => 'images/materials/greenply.png', 'alt' => 'Premium wooden entrance door', 'name' => 'Greenply', 'type' => 'Premium Doors', 'specs' => ['Solid Core', 'Termite Proof', 'Acoustic Seal']],
    ['category' => 'wood',   'img' => 'materials/centuryply.png', 'srcset' => 'images/materials/centuryply.png 1112w', 'bg_src' => 'images/materials/centuryply.png', 'alt' => 'Premium hardwood flooring and wood materials', 'name' => 'Century', 'type' => 'Hardwood &amp; Plywood', 'specs' => ['BWP Grade', 'Eco Certified', 'Long Lasting']],
    ['category' => 'bath',   'img' => 'materials/jaquar.png', 'srcset' => 'images/materials/jaquar.png 1112w', 'bg_src' => 'images/materials/jaquar.png', 'alt' => 'Luxury bathroom fittings and fixtures', 'name' => 'Jaquar', 'type' => 'Bath Fittings', 'specs' => ['Chrome Finish', 'Water Saving', '10 Year Warranty']],
    ['category' => 'electrical', 'img' => 'materials/legrand.png', 'srcset' => 'images/materials/legrand.png 1112w', 'bg_src' => 'images/materials/legrand.png', 'alt' => 'Premium electrical switches and wiring', 'name' => 'Legrand', 'type' => 'Electrical Systems', 'specs' => ['Smart Ready', 'Fire Retardant', 'Modular Design']],
    ['category' => 'paints', 'img' => 'materials/asian_paints.png', 'srcset' => 'images/materials/asian_paints.png 1112w', 'bg_src' => 'images/materials/asian_paints.png', 'alt' => 'Premium interior paint finishes', 'name' => 'Asian Paints', 'type' => 'Premium Paints', 'specs' => ['Low VOC', 'Washable Finish', 'Fade Resistant']],
    ['category' => 'plumbing','img' => 'materials/astral.png', 'srcset' => 'images/materials/astral.png 1112w', 'bg_src' => 'images/materials/astral.png', 'alt' => 'Premium plumbing pipes and fittings', 'name' => 'Astral', 'type' => 'Plumbing Solutions', 'specs' => ['CPVC Grade', 'Leak Proof', 'Heat Resistant']],
    ['category' => 'switches','img' => 'materials/havells.png', 'srcset' => 'images/materials/havells.png 1112w', 'bg_src' => 'images/materials/havells.png', 'alt' => 'Modern smart home switches and controls', 'name' => 'Havells', 'type' => 'Smart Switches', 'specs' => ['Touch Control', 'App Compatible', 'Elegant Finish']],
    ['category' => 'doors',  'img' => 'materials/hettich.png', 'srcset' => 'images/materials/hettich.png 1112w', 'bg_src' => 'images/materials/hettich.png', 'alt' => 'Luxury drawer systems and hardware', 'name' => 'Hettich', 'type' => 'Drawer Systems', 'specs' => ['Soft Close', 'German Engineering', 'Silent Motion']],
];
$contactUrl = $site['contact_url'] ?? 'contact.php';
$materialCardSizes = '(max-width: 640px) 88vw, (max-width: 1200px) 42vw, 360px';
?>
<section id="materials" class="materials-section h-section">
  <div class="h-pin">
    <div class="materials-bg" aria-hidden="true"></div>
    <div class="h-track" id="materialsTrack" aria-label="Materials showcase" tabindex="0">
      <div class="h-head">
        <span class="eyebrow">Materials</span>
        <h2>Premium Materials We Use</h2>
        <p>Italian finishes, structural steel, engineered wood, smart systems — every material is selected to age beautifully and perform for decades.</p>
        <a href="<?php echo e($contactUrl); ?>" class="btn btn--solid materials-section__cta">Request a Quote</a>
        <div class="materials-filter" aria-label="Material categories">
          <?php foreach ($filters as $i => $filter): ?>
            <button class="materials-filter__btn<?php echo $i === 0 ? ' active' : ''; ?>" data-filter="<?php echo htmlspecialchars($filter['key']); ?>" aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>">
              <i class="fa-solid <?php echo $filter['icon']; ?>" aria-hidden="true"></i>
              <span><?php echo $filter['label']; ?></span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <?php foreach ($materials as $material): ?>
        <article class="material-card" data-category="<?php echo htmlspecialchars($material['category']); ?>">
          <div class="material-card__img-wrap">
            <img src="images/<?php echo $material['img']; ?>"
                 srcset="<?php echo $material['srcset']; ?>"
                 sizes="<?php echo $materialCardSizes; ?>"
                 data-bg-src="<?php echo e($material['bg_src']); ?>"
                 alt="<?php echo htmlspecialchars($material['alt']); ?>"
                 loading="lazy"
                 decoding="async"
                 width="640"
                 height="427">
          </div>
          <div class="material-card__body">
            <h3><?php echo $material['name']; ?></h3>
            <p class="material-card__type"><?php echo $material['type']; ?></p>
            <ul class="material-card__specs">
              <?php foreach ($material['specs'] as $spec): ?>
                <li><?php echo $spec; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <div class="progress-bar" aria-hidden="true"><i id="materialsProgress"></i></div>
  </div>
</section>
