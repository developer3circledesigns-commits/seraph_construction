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
    ['category' => 'tiles',  'img' => 'materials/top-view-boards-mdf-material.webp', 'srcset' => 'images/materials/top-view-boards-mdf-material.webp 1112w', 'bg_src' => 'images/materials/top-view-boards-mdf-material.webp', 'alt' => 'Premium Italian marble tiles', 'name' => 'Kajaria', 'type' => 'Premium Tiles', 'specs' => ['Italian Finish', 'Water Resistant', 'Scratch Proof']],
    ['category' => 'steel',  'img' => 'materials/outdoor-tourism-building-old-bridge.webp', 'srcset' => 'images/materials/outdoor-tourism-building-old-bridge.webp 1112w', 'bg_src' => 'images/materials/outdoor-tourism-building-old-bridge.webp', 'alt' => 'Structural steel beams for construction', 'name' => 'Tata Steel', 'type' => 'Structural Steel', 'specs' => ['High Tensile', 'Corrosion Resistant', 'ISI Certified']],
    ['category' => 'doors',  'img' => 'materials/greenply-materials-style-darkluxury.webp', 'srcset' => 'images/materials/greenply-materials-style-darkluxury.webp 1112w', 'bg_src' => 'images/materials/greenply-materials-style-darkluxury.webp', 'alt' => 'Premium wooden entrance door', 'name' => 'Greenply', 'type' => 'Premium Doors', 'specs' => ['Solid Core', 'Termite Proof', 'Acoustic Seal']],
    ['category' => 'wood',   'img' => 'materials/pile-wood-planks-front-view.webp', 'srcset' => 'images/materials/pile-wood-planks-front-view.webp 1112w', 'bg_src' => 'images/materials/pile-wood-planks-front-view.webp', 'alt' => 'Premium hardwood flooring and wood materials', 'name' => 'Century', 'type' => 'Hardwood &amp; Plywood', 'specs' => ['BWP Grade', 'Eco Certified', 'Long Lasting']],
    ['category' => 'bath',   'img' => 'materials/jaquar-bath-fittings-no-text.webp', 'srcset' => 'images/materials/jaquar-bath-fittings-no-text.webp 1112w', 'bg_src' => 'images/materials/jaquar-bath-fittings-no-text.webp', 'alt' => 'Luxury bathroom fittings and fixtures', 'name' => 'Jaquar', 'type' => 'Bath Fittings', 'specs' => ['Chrome Finish', 'Water Saving', '10 Year Warranty']],
    ['category' => 'electrical', 'img' => 'materials/electrician-with-tablet-speed-testing-digital-switchboard-monitoring.webp', 'srcset' => 'images/materials/electrician-with-tablet-speed-testing-digital-switchboard-monitoring.webp 1112w', 'bg_src' => 'images/materials/electrician-with-tablet-speed-testing-digital-switchboard-monitoring.webp', 'alt' => 'Premium electrical switches and wiring', 'name' => 'Legrand', 'type' => 'Electrical Systems', 'specs' => ['Smart Ready', 'Fire Retardant', 'Modular Design']],
    ['category' => 'paints', 'img' => 'materials/asian-paints-buckets-modern-enhanced.webp', 'srcset' => 'images/materials/asian-paints-buckets-modern-enhanced.webp 1112w', 'bg_src' => 'images/materials/asian-paints-buckets-modern-enhanced.webp', 'alt' => 'Premium interior paint finishes', 'name' => 'Asian Paints', 'type' => 'Premium Paints', 'specs' => ['Low VOC', 'Washable Finish', 'Fade Resistant']],
    ['category' => 'plumbing','img' => 'materials/astral-fire-pro-pipes-modern-enhanced.webp', 'srcset' => 'images/materials/astral-fire-pro-pipes-modern-enhanced.webp 1112w', 'bg_src' => 'images/materials/astral-fire-pro-pipes-modern-enhanced.webp', 'alt' => 'Premium plumbing pipes and fittings', 'name' => 'Astral', 'type' => 'Plumbing Solutions', 'specs' => ['CPVC Grade', 'Leak Proof', 'Heat Resistant']],
    ['category' => 'switches','img' => 'materials/havells-products-lifestyle.webp', 'srcset' => 'images/materials/havells-products-lifestyle.webp 1112w', 'bg_src' => 'images/materials/havells-products-lifestyle.webp', 'alt' => 'Modern smart home switches and controls', 'name' => 'Havells', 'type' => 'Smart Switches', 'specs' => ['Touch Control', 'App Compatible', 'Elegant Finish']],
    ['category' => 'doors',  'img' => 'materials/open-kitchen-drawer-with-storage-system-modern-cabinets-functional-kitchen-furniture-detail.webp', 'srcset' => 'images/materials/open-kitchen-drawer-with-storage-system-modern-cabinets-functional-kitchen-furniture-detail.webp 1112w', 'bg_src' => 'images/materials/open-kitchen-drawer-with-storage-system-modern-cabinets-functional-kitchen-furniture-detail.webp', 'alt' => 'Luxury drawer systems and hardware', 'name' => 'Hettich', 'type' => 'Drawer Systems', 'specs' => ['Soft Close', 'German Engineering', 'Silent Motion']],
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
