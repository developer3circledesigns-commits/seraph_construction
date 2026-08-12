<!-- =====================================================
     3. HOME PLAN — Scroll-driven 5-image sequence
     ===================================================== -->
<?php $contactUrl = $site['contact_url'] ?? 'contact.php'; ?>
<section id="homeplan" class="blur-panel kitchen-sequence">
  <div class="kitchen-sequence__stack" aria-hidden="true">
    <?php
    $steps = [
        ['img' => 'elevation',       'alt' => 'Front elevation of the luxury home showing architectural facade'],
        ['img' => 'livingroom',      'alt' => 'Open living room with high ceilings, sectional sofa and modern staircase'],
        ['img' => 'bedroom',         'alt' => 'Serene master bedroom with warm cabinetry and soft lighting'],
        ['img' => 'modularkitchen',  'alt' => 'Modern modular kitchen with marble island and premium cabinetry'],
        ['img' => 'toilet',          'alt' => 'Luxury toilet and bathroom with stone finish and premium fittings'],
    ];
    foreach ($steps as $step): ?>
    <div class="kitchen-sequence__img">
      <img
        src="images/<?php echo $step['img']; ?>@1112w.webp"
        srcset="images/<?php echo $step['img']; ?>@1112w.webp 1112w, images/<?php echo $step['img']; ?>@2230w.webp 2230w"
        sizes="(min-width: 1400px) 1112px, 100vw"
        alt="<?php echo htmlspecialchars($step['alt']); ?>"
        width="1400"
        height="940"
        loading="lazy"
        decoding="async"
      >
    </div>
    <?php endforeach; ?>
  </div>
  <div class="blur-panel__content kitchen-sequence__content">
    <div class="kitchen-sequence__copy-stack">
      <div class="kitchen-sequence__copy is-active" data-step="1">
        <span class="eyebrow">Home Plan &middot; 01 &mdash; Elevation</span>
        <h2>Front Elevation</h2>
        <p>The architectural facade that welcomes you home &mdash; materials, massing and light composed into a timeless silhouette.</p>
      </div>
      <div class="kitchen-sequence__copy" data-step="2">
        <span class="eyebrow">Home Plan &middot; 02 &mdash; Living Room</span>
        <h2>Living Room</h2>
        <p>High ceilings, tailored joinery and generous seating engineered for relaxation, togetherness and comfortable entertaining.</p>
      </div>
      <div class="kitchen-sequence__copy" data-step="3">
        <span class="eyebrow">Home Plan &middot; 03 &mdash; Bed Room</span>
        <h2>Bed Room</h2>
        <p>Soft textures, warm cabinetry and layered lighting tuned to slow the pace of the day into a deep and restful calm.</p>
      </div>
      <div class="kitchen-sequence__copy" data-step="4">
        <span class="eyebrow">Home Plan &middot; 04 &mdash; Kitchen</span>
        <h2>Modular Kitchen</h2>
        <p>German-engineered units, premium stone counters and smart storage laid out around the way you actually cook.</p>
      </div>
      <div class="kitchen-sequence__copy" data-step="5">
        <span class="eyebrow">Home Plan &middot; 05 &mdash; Toilet</span>
        <h2>Toilet &amp; Bath</h2>
        <p>A spa-like retreat finished in stone and chrome with premium fittings engineered to serve beautifully for decades.</p>
      </div>
    </div>
    <a href="<?php echo e($contactUrl); ?>?service=construction" class="btn btn--solid">Explore Home Plan</a>
    <div class="kitchen-sequence__progress" aria-hidden="true">
      <span class="kitchen-sequence__counter"><span id="kitchenStepCurrent">1</span> / 5</span>
      <div class="kitchen-sequence__dots">
        <span class="is-active"></span><span></span><span></span><span></span><span></span>
      </div>
    </div>
  </div>
</section>