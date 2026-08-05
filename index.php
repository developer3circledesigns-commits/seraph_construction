<?php
/**
 * SERAPH BUILD CONSTRUCTION — PHP entry point.
 * Assembles the single-page site from reusable partials.
 */

$site = require __DIR__ . '/config/site.php';

require __DIR__ . '/partials/header.php';
?>
<main id="main-content">
  <?php
  require __DIR__ . '/partials/sections/hero.php';
  require __DIR__ . '/partials/sections/interior.php';
  require __DIR__ . '/partials/sections/homeplan.php';
  require __DIR__ . '/partials/sections/materials.php';
  require __DIR__ . '/partials/sections/services.php';
  require __DIR__ . '/partials/sections/projects.php';
  require __DIR__ . '/partials/sections/about.php';
  require __DIR__ . '/partials/sections/testimonials.php';
  ?>
</main>
<?php
require __DIR__ . '/partials/footer.php';
