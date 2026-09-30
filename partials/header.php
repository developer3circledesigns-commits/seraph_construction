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
$ogUrl    = $ogBase . ($_SERVER['REQUEST_URI'] ?? '/');

// Optional per-page SEO. Pages may set $pageMeta = ['title','description',
// 'keywords','og_title','og_description','og_image','og_image_alt'] before
// including this partial. Anything omitted falls back to the site defaults,
// so existing pages keep rendering exactly as before.
$pageMeta = is_array($pageMeta ?? null) ? $pageMeta : [];

$metaTitle       = $pageMeta['title'] ?? ($site['name'] . ' | ' . $site['tagline']);
$metaDescription = $pageMeta['description'] ?? 'SERAPH BUILD CONSTRUCTION delivers premium construction, interior design, and commercial projects across Chennai with timeless craftsmanship.';
$metaKeywords    = $pageMeta['keywords'] ?? 'luxury construction, architecture, interior design, modular kitchen, premium materials, commercial construction';
$metaRobots      = $pageMeta['robots'] ?? 'index, follow';
$ogTitle         = $pageMeta['og_title'] ?? ($site['name'] . ' | Premium Luxury Architecture');
$ogDescription   = $pageMeta['og_description'] ?? 'Building premium spaces. Creating timeless experiences. Luxury construction, interiors & architecture.';
$ogImagePath     = $pageMeta['og_image'] ?? '/images/hero-seraph@1672w.webp';
$ogImageAlt      = $pageMeta['og_image_alt'] ?? 'Luxury modern villa exterior — SERAPH BUILD CONSTRUCTION';
$ogImage         = preg_match('#^https?://#i', $ogImagePath) ? $ogImagePath : $ogBase . $ogImagePath;
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
  <title><?php echo htmlspecialchars($metaTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
  <meta name="author" content="SERAPH BUILD CONSTRUCTION">
  <meta name="robots" content="<?php echo htmlspecialchars($metaRobots); ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($ogUrl); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
  <meta property="og:image:width" content="1672">
  <meta property="og:image:height" content="942">
  <meta property="og:image:alt" content="<?php echo htmlspecialchars($ogImageAlt); ?>">
  <meta property="og:site_name" content="SERAPH BUILD CONSTRUCTION">
  <meta property="og:locale" content="en_IN">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">
  <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($ogImageAlt); ?>">

  <!-- Favicon -->
  <link rel="icon" href="favicon.ico" sizes="48x48">
  <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="192x192" href="images/favicon-192.png">
  <link rel="icon" type="image/png" sizes="512x512" href="images/favicon-512.png">
  <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">

  <!-- Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

  <!-- Google Fonts (non-render-blocking: preloaded here, applied by js/async-css.js after parse) -->
  <!-- Montserrat 800 is loaded for the topbar nav labels; without it the
       800 request falls back to faux-bold and renders uneven. -->
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"></noscript>

  <!-- Font Awesome (non-render-blocking: preloaded here, applied by js/async-css.js after parse) -->
  <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>
  <script src="js/async-css.js" defer></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
<?php /* Hero LCP uses fetchpriority="high" in partials/sections/hero.php. A manual
       <link rel=preload> here caused a duplicate network fetch of the hero
       (and an aborted ~200KB request on mobile), so it is intentionally not
       emitted — the preload scanner already finds the hero img immediately. */ ?>
  <link rel="stylesheet" href="css/responsive.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="css/responsive.css"></noscript>

<?php
// Optional extra stylesheets declared by the page itself, e.g.
// packages.php: 'styles' => ['css/packages/packages-base.css'].
// .htaccess serves CSS with a one-year immutable cache, so a ?v=
// derived from the file's mtime is what keeps an edited stylesheet
// from being served stale.
foreach ((array) ($pageMeta['styles'] ?? []) as $skStyle) {
    $skHref = (string) $skStyle;
    $skPath = ROOT_PATH . '/' . ltrim($skHref, '/');
    if (is_file($skPath)) {
        $skHref .= '?v=' . filemtime($skPath);
    }
    echo '  <link rel="stylesheet" href="' . htmlspecialchars($skHref) . '">' . "\n";
}

// Optional extra webfonts for a page that needs to break from the
// site's Montserrat/Poppins pairing — the Dossier layout sets a serif.
// display=swap means text paints in the fallback immediately, so this
// never blocks rendering.
foreach ((array) ($pageMeta['fonts'] ?? []) as $skFont) {
    echo '  <link rel="stylesheet" href="' . htmlspecialchars((string) $skFont) . '">' . "\n";
}
?>

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