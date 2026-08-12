<?php
/**
 * Global head / header partial.
 * Expects $site array to be available.
 */
$site = $site ?? require __DIR__ . '/../config/site.php';

// Load the shared API core (session + CSRF only; the public marketing site
// performs no database work) so the nav can render protected login forms.
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../api/config/bootstrap.php';
}

$ogScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$ogHost   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$ogBase   = $ogScheme . '://' . $ogHost;
$ogImage  = $ogBase . '/images/hero-front@1680w.webp';
$ogUrl    = $ogBase . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="theme-color" content="#001431">
  <meta name="color-scheme" content="dark">

  <!-- SEO -->
  <title><?php echo htmlspecialchars($site['name']); ?> | <?php echo htmlspecialchars($site['tagline']); ?></title>
  <meta name="description" content="SERAPH BUILD CONSTRUCTION delivers premium construction, interior design, and commercial projects across Chennai with timeless craftsmanship.">
  <meta name="keywords" content="luxury construction, architecture, interior design, modular kitchen, premium materials, commercial construction">
  <meta name="author" content="SERAPH BUILD CONSTRUCTION">
  <meta name="robots" content="index, follow">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="SERAPH BUILD CONSTRUCTION | Premium Luxury Architecture">
  <meta property="og:description" content="Building premium spaces. Creating timeless experiences. Luxury construction, interiors & architecture.">
  <meta property="og:url" content="<?php echo htmlspecialchars($ogUrl); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
  <meta property="og:image:width" content="1680">
  <meta property="og:image:height" content="945">
  <meta property="og:image:alt" content="Luxury modern villa exterior — SERAPH BUILD CONSTRUCTION">
  <meta property="og:site_name" content="SERAPH BUILD CONSTRUCTION">
  <meta property="og:locale" content="en_IN">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="SERAPH BUILD CONSTRUCTION | Premium Luxury Architecture">
  <meta name="twitter:description" content="Building premium spaces. Creating timeless experiences.">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">
  <meta name="twitter:image:alt" content="Luxury modern villa exterior — SERAPH BUILD CONSTRUCTION">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Cpolygon fill='%23C79A56' points='20,2 38,38 2,38'/%3E%3Cpolygon fill='%23090909' points='20,12 30,34 10,34'/%3E%3C/svg%3E">

  <!-- Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

  <!-- Google Fonts (non-render-blocking: preloaded here, applied by js/async-css.js after parse) -->
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"></noscript>

  <!-- Font Awesome (non-render-blocking: preloaded here, applied by js/async-css.js after parse) -->
  <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>
  <script src="js/async-css.js" defer></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="preload" as="image" type="image/webp" href="images/hero-front@1120w.webp"
        imagesrcset="images/hero-front@1120w.webp 1120w, images/hero-front@1680w.webp 1680w"
        imagesizes="(min-width: 1400px) 1200px, 100vw">
  <link rel="stylesheet" href="css/responsive.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="css/responsive.css"></noscript>

  <!-- Schema Markup -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "SERAPH BUILD CONSTRUCTION",
    "description": "Premium luxury construction, interior design, and commercial company.",
    "founder": {
      "@type": "Person",
      "name": "Sureshkumar .M",
      "jobTitle": "Founder & CEO"
    },
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "715-A, 7th Floor, Spencer Plaza, Anna Salai",
      "addressLocality": "Chennai",
      "addressRegion": "TN",
      "postalCode": "600002",
      "addressCountry": "IN"
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+91-90925-57722",
      "contactType": "sales",
      "email": "seraphbuildconstruction@gmail.com"
    },
    "sameAs": [
      "https://facebook.com/seraphconstruction",
      "https://instagram.com/seraphconstruction",
      "https://linkedin.com/company/seraphconstruction",
      "https://youtube.com/@seraphconstruction"
    ]
  }
  </script>
</head>
<body>

  <a class="skip-link" href="#main-content">Skip to main content</a>

  <?php include __DIR__ . '/nav.php'; ?>