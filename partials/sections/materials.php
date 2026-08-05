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

$materials = [
    ['category' => 'tiles',  'img' => 'top-view-boards-mdf-material.webp', 'alt' => 'Premium Italian marble tiles', 'name' => 'Kajaria', 'type' => 'Premium Tiles', 'specs' => ['Italian Finish', 'Water Resistant', 'Scratch Proof']],
    ['category' => 'steel',  'img' => 'outdoor-tourism-building-old-bridge.webp', 'alt' => 'Structural steel beams for construction', 'name' => 'Tata Steel', 'type' => 'Structural Steel', 'specs' => ['High Tensile', 'Corrosion Resistant', 'ISI Certified']],
    ['category' => 'doors',  'img' => 'greenply-materials-style-darkluxury.webp', 'alt' => 'Premium wooden entrance door', 'name' => 'Greenply', 'type' => 'Premium Doors', 'specs' => ['Solid Core', 'Termite Proof', 'Acoustic Seal']],
    ['category' => 'wood',   'img' => 'pile-wood-planks-front-view.webp', 'alt' => 'Premium hardwood flooring and wood materials', 'name' => 'Century', 'type' => 'Hardwood &amp; Plywood', 'specs' => ['BWP Grade', 'Eco Certified', 'Long Lasting']],
    ['category' => 'bath',   'img' => 'jaquar-bath-fittings-no-text.webp', 'alt' => 'Luxury bathroom fittings and fixtures', 'name' => 'Jaquar', 'type' => 'Bath Fittings', 'specs' => ['Chrome Finish', 'Water Saving', '10 Year Warranty']],
    ['category' => 'electrical', 'img' => 'electrician-with-tablet-speed-testing-digital-switchboard-monitoring.webp', 'alt' => 'Premium electrical switches and wiring', 'name' => 'Legrand', 'type' => 'Electrical Systems', 'specs' => ['Smart Ready', 'Fire Retardant', 'Modular Design']],
    ['category' => 'paints', 'img' => 'asian-paints-buckets-modern-enhanced.webp', 'alt' => 'Premium interior paint finishes', 'name' => 'Asian Paints', 'type' => 'Premium Paints', 'specs' => ['Low VOC', 'Washable Finish', 'Fade Resistant']],
    ['category' => 'plumbing','img' => 'astral-fire-pro-pipes-modern-enhanced.webp', 'alt' => 'Premium plumbing pipes and fittings', 'name' => 'Astral', 'type' => 'Plumbing Solutions', 'specs' => ['CPVC Grade', 'Leak Proof', 'Heat Resistant']],
    ['category' => 'switches','img' => 'havells-products-lifestyle.webp', 'alt' => 'Modern smart home switches and controls', 'name' => 'Havells', 'type' => 'Smart Switches', 'specs' => ['Touch Control', 'App Compatible', 'Elegant Finish']],
    ['category' => 'doors',  'img' => 'open-kitchen-drawer-with-storage-system-modern-cabinets-functional-kitchen-furniture-detail.webp', 'alt' => 'Luxury drawer systems and hardware', 'name' => 'Hettich', 'type' => 'Drawer Systems', 'specs' => ['Soft Close', 'German Engineering', 'Silent Motion']],
];
?>
<section id="materials" class="materials-section h-section">
  <div class="h-pin">
    <div class="materials-bg" aria-hidden="true"></div>
    <div class="h-track" id="materialsTrack" aria-label="Materials showcase" tabindex="0">
      <div class="h-head">
        <span class="eyebrow">Materials</span>
        <h2>Premium Materials We Use</h2>
        <p>Italian finishes, structural steel, engineered wood, smart systems — every material is selected to age beautifully and perform for decades.</p>
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
            <img src="images/materials/<?php echo $material['img']; ?>" alt="<?php echo htmlspecialchars($material['alt']); ?>" loading="lazy" width="1280" height="854">
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