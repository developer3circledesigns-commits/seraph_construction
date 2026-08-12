<!-- =====================================================
     5. SERVICES — premium service cards
     ===================================================== -->
<?php
$services = [
    ['icon' => 'fa-building',      'title' => 'Construction and Elevation', 'text' => 'Turnkey residential and commercial construction delivered with premium finishes, precision engineering, and on-schedule handover.'],
    ['icon' => 'fa-pen-ruler',     'title' => 'Interior Design',             'text' => 'Bespoke interiors that blend function with artistry — from concept and 3D visualisation to flawless execution.'],
    ['icon' => 'fa-kitchen-set',   'title' => 'Modular Kitchens',            'text' => 'German-engineered modular kitchens with premium hardware, smart storage, and seamless cabinetry design.'],
    ['icon' => 'fa-hammer',        'title' => 'Commercial',                  'text' => 'Transform existing spaces with structural upgrades, premium remodelling, and refined finish restoration.'],
    ['icon' => 'fa-clipboard-check','title' => 'Project Management',         'text' => 'End-to-end coordination of design, procurement, and site teams — transparent budgeting, zero surprises.'],
    ['icon' => 'fa-compass-drafting','title' => 'Architectural Planning',    'text' => 'Concept-to-completion planning and approvals with a dedicated in-house architecture team.'],
];
$contactUrl = $site['contact_url'] ?? 'contact.php';
?>
<section id="services" class="services-section">
  <div class="container">
    <span class="eyebrow">What We Do</span>
    <h2 class="services-section__heading">Our Services</h2>
    <p class="services-section__intro">From ground-breaking to final handover, we deliver the complete spectrum of premium construction and design services.</p>
    <p class="services-section__cta-wrap"><a href="<?php echo e($contactUrl); ?>" class="btn btn--solid">Get a Quote</a></p>
    <div class="services-grid">
      <?php foreach ($services as $service): ?>
        <article class="service-card glass-card">
          <div class="service-card__icon"><i class="fa-solid <?php echo $service['icon']; ?>" aria-hidden="true"></i></div>
          <h3><?php echo $service['title']; ?></h3>
          <p><?php echo $service['text']; ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>