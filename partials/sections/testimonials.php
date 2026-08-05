<!-- =====================================================
     8. TESTIMONIALS — Testimonials 01: Single Split Carousel
     ===================================================== -->
<?php
$testimonials = [
    ['quote' => 'They rebuilt our villa to the millimetre. Every deadline met, every detail honoured — the most painless build we have ever had.', 'who' => 'Anita Sharma', 'role' => 'Villa Owner &middot; Palm Meadows'],
    ['quote' => 'The modular kitchen is flawless — soft-close everything, perfect lighting, and the team finished a week early.', 'who' => 'Rohit Menon', 'role' => 'Homeowner &middot; Residency Towers'],
    ['quote' => 'From concept to keys, one team handled everything. Transparent pricing and a finish that still turns heads three years on.', 'who' => 'Meera Iyer', 'role' => 'Interior Client &middot; The Crest'],
];
?>
<section id="testimonials" class="testimonials-section">
  <div class="container">
    <div class="quotes-head">
      <span class="eyebrow" data-reveal>Testimonials</span>
      <h2 data-reveal>What Our Clients Say</h2>
      <p data-reveal>One voice at a time. Click or tap through the carousel.</p>
    </div>

    <div class="carousel" id="quotesCarousel">
      <?php foreach ($testimonials as $i => $t): ?>
        <article class="slide<?php echo $i === 0 ? ' is-active' : ''; ?>">
          <div class="slide__portrait" aria-hidden="true"><i class="fa-solid fa-user" aria-hidden="true"></i></div>
          <div class="slide__quote">
            <blockquote><?php echo $t['quote']; ?></blockquote>
            <div class="who"><?php echo $t['who']; ?></div>
            <div class="role"><?php echo $t['role']; ?></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="controls">
      <button id="prevBtn" aria-label="Previous testimonial">&#8592;</button>
      <button id="nextBtn" aria-label="Next testimonial">&#8594;</button>
      <div class="dots" id="dots"></div>
    </div>
  </div>
</section>