<!-- =====================================================
     7. ABOUT — About 01: Split Reveal
     ===================================================== -->
<section id="about" class="about-section">
  <div class="container">
    <div class="about-head">
      <span class="eyebrow" data-reveal>About Us</span>
      <h2>
        <span class="line"><span class="line-inner">Built on Craft,</span></span>
        <span class="line"><span class="line-inner">Driven by Detail</span></span>
      </h2>
      <p data-reveal>SERAPH BUILD CONSTRUCTION is a full-service design and build studio trusted by discerning clients for two decades. We unite architecture, engineering and craftsmanship under one roof to deliver spaces of enduring quality.</p>
    </div>

    <div class="split">
      <div class="split__media" data-img>
        <img src="images/realistic-construction-site-no-human.webp" alt="Luxury interior design" width="1000" height="667" loading="lazy" decoding="async">
      </div>
      <div class="split__body">
        <span class="eyebrow" data-reveal>Our Story</span>
        <h3 data-reveal>From First Sketch to Final Walkthrough</h3>
        <p data-reveal>Every project begins with listening. We pair architectural thinking with hands-on construction so nothing is lost between drawing and build.</p>
        <p data-reveal>Our in-house carpenters, masons and engineers treat each site like a signature — because to us, it is.</p>
        <a href="contact.php" class="btn" data-reveal>Meet the Team</a>
      </div>
    </div>

    <div class="split split--flip">
      <div class="split__media" data-img>
        <img src="images/our-approach-one-team.webp" alt="Modular kitchen" width="1000" height="667" loading="lazy" decoding="async">
      </div>
      <div class="split__body">
        <span class="eyebrow" data-reveal>Our Approach</span>
        <h3 data-reveal>One Team, One Promise</h3>
        <p data-reveal>Architecture, interiors, modular kitchens and renovation — delivered by a single accountable team from concept to keys.</p>
        <p data-reveal>That means one budget, one timeline and one standard: flawless.</p>
        <a href="#services" class="btn" data-reveal>See How We Work</a>
      </div>
    </div>

    <div class="stats">
      <div class="stats-grid">
        <?php
        $stats = [
            ['target' => 150, 'suffix' => '+', 'label' => 'Projects Delivered'],
            ['target' => 20,  'suffix' => '+', 'label' => 'Years of Craft'],
            ['target' => 98,  'suffix' => '%', 'label' => 'Client Satisfaction'],
            ['target' => 50,  'suffix' => '+', 'label' => 'In-House Experts'],
        ];
        foreach ($stats as $stat): ?>
        <div class="stat">
          <div class="num"><span class="counter" data-target="<?php echo $stat['target']; ?>">0</span><sup><?php echo $stat['suffix']; ?></sup></div>
          <p><?php echo $stat['label']; ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>