<?php
/**
 * SERAPH BUILD CONSTRUCTION — Download project layout (sample).
 * Serves a lightweight layout sheet (HTML) for a given project so the
 * "Download Layout" buttons on the projects page work out of the box.
 */
$site = require __DIR__ . '/config/site.php';

$projects = [
    '01' => ['no' => '01', 'title' => 'Villa Seraph', 'img' => 'images/projects/theme-home-daylight-warm.webp',
        'location' => 'Mumbai', 'plot' => '6,200 sq.ft.', 'builtup' => '12,000 sq.ft.', 'floors' => '4', 'bedrooms' => '5', 'bathrooms' => '6', 'style' => 'Contemporary Luxury'],
    '02' => ['no' => '02', 'title' => 'The Glasshouse', 'img' => 'images/glass_house@1000w.webp',
        'location' => 'Pune', 'plot' => '4,800 sq.ft.', 'builtup' => '8,400 sq.ft.', 'floors' => '3', 'bedrooms' => '4', 'bathrooms' => '4', 'style' => 'Modern Minimal'],
    '03' => ['no' => '03', 'title' => 'Hotel Aurelia', 'img' => 'images/hotel_aurelia@1000w.webp',
        'location' => 'Jaipur', 'plot' => '18,000 sq.ft.', 'builtup' => '42,000 sq.ft.', 'floors' => '6', 'bedrooms' => '65', 'bathrooms' => '72', 'style' => 'Heritage Boutique'],
    '04' => ['no' => '04', 'title' => 'Penthouse Noir', 'img' => 'images/projects/theme-penthouse-light-camel.webp',
        'location' => 'Bengaluru', 'plot' => '—', 'builtup' => '4,200 sq.ft.', 'floors' => '2', 'bedrooms' => '4', 'bathrooms' => '5', 'style' => 'Dark Luxe'],
    '05' => ['no' => '05', 'title' => 'Garden Pavilion', 'img' => 'images/garden@1000w.webp',
        'location' => 'Delhi', 'plot' => '3,600 sq.ft.', 'builtup' => '6,800 sq.ft.', 'floors' => '3', 'bedrooms' => '4', 'bathrooms' => '4', 'style' => 'Heritage Revival'],
    '06' => ['no' => '06', 'title' => 'Skyline Offices', 'img' => 'images/projects/theme-offices-silver-blue.webp',
        'location' => 'Noida', 'plot' => '9,400 sq.ft.', 'builtup' => '26,500 sq.ft.', 'floors' => '5', 'bedrooms' => '0', 'bathrooms' => '10', 'style' => 'Corporate Modern'],
    '07' => ['no' => '07', 'title' => 'The Aurelia Villa', 'img' => 'images/livingroom@1112w.webp',
        'location' => 'Goa', 'plot' => '7,800 sq.ft.', 'builtup' => '11,200 sq.ft.', 'floors' => '3', 'bedrooms' => '5', 'bathrooms' => '6', 'style' => 'Coastal Luxury'],
    '08' => ['no' => '08', 'title' => 'Serene Penthouse', 'img' => 'images/bedroom@1112w.webp',
        'location' => 'Hyderabad', 'plot' => '—', 'builtup' => '3,900 sq.ft.', 'floors' => '2', 'bedrooms' => '3', 'bathrooms' => '4', 'style' => 'Warm Contemporary'],
    '09' => ['no' => '09', 'title' => 'Maison Kitchen', 'img' => 'images/modularkitchen@1112w.webp',
        'location' => 'Chennai', 'plot' => '—', 'builtup' => '450 sq.ft.', 'floors' => '1', 'bedrooms' => '0', 'bathrooms' => '1', 'style' => 'Minimal Kitchen'],
    '10' => ['no' => '10', 'title' => 'Sanctuary Home', 'img' => 'images/interior-living-room@768w.webp',
        'location' => 'Coimbatore', 'plot' => '5,200 sq.ft.', 'builtup' => '7,600 sq.ft.', 'floors' => '3', 'bedrooms' => '4', 'bathrooms' => '5', 'style' => 'Modern Tropical'],
    '11' => ['no' => '11', 'title' => 'Elevate Residence', 'img' => 'images/elevation@1112w.webp',
        'location' => 'Chennai', 'plot' => '—', 'builtup' => '3,100 sq.ft.', 'floors' => '2', 'bedrooms' => '3', 'bathrooms' => '3', 'style' => 'Urban Minimal'],
    '12' => ['no' => '12', 'title' => 'Bath & Beyond', 'img' => 'images/toilet@1112w.webp',
        'location' => 'Bengaluru', 'plot' => '—', 'builtup' => '1,100 sq.ft.', 'floors' => '1', 'bedrooms' => '0', 'bathrooms' => '6', 'style' => 'Spa Minimal'],
];

$id = (string)($_GET['p'] ?? '');
$p  = $projects[$id] ?? null;

// Redirect to the projects page when the project is unknown.
if (!$p) {
    header('Location: projects.php');
    exit;
}

$slug = strtolower(str_replace(' ', '-', $p['title']));
$filename = 'seraph-' . $slug . '-layout.html';

header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($p['title']); ?> — Project Layout</title>
<style>
  * , *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #ffffff; color: #16140f; padding: 48px; }
  .sheet { max-width: 840px; margin: 0 auto; border: 1px solid #d9d2c4; }
  .sheet__head { background: #0e0d0b; color: #f4efe8; padding: 32px 40px; }
  .sheet__head h1 { font-size: 2rem; font-weight: 600; }
  .sheet__head p { color: #C79A56; letter-spacing: 0.15em; text-transform: uppercase; font-size: 0.8rem; margin-top: 6px; }
  .sheet__body { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding: 32px 40px; }
  .sheet__body img { width: 100%; border: 1px solid #d9d2c4; }
  .spec-table { width: 100%; border-collapse: collapse; }
  .spec-table td { padding: 10px 12px; border-bottom: 1px solid #e5e0d6; font-size: 0.95rem; }
  .spec-table td:first-child { color: #8a8377; width: 50%; }
  .sheet__foot { padding: 20px 40px 28px; border-top: 1px solid #e5e0d6; color: #8a8377; font-size: 0.85rem; }
</style>
</head>
<body>
  <div class="sheet">
    <div class="sheet__head">
      <h1><?php echo htmlspecialchars($p['title']); ?></h1>
      <p><?php echo htmlspecialchars($site['name']); ?></p>
    </div>
    <div class="sheet__body">
      <img src="<?php echo htmlspecialchars($p['img']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>">
      <table class="spec-table">
        <tr><td>Project No.</td><td><?php echo $p['no']; ?></td></tr>
        <tr><td>Location</td><td><?php echo $p['location']; ?></td></tr>
        <tr><td>Plot Size</td><td><?php echo $p['plot']; ?></td></tr>
        <tr><td>Built-up Area</td><td><?php echo $p['builtup']; ?></td></tr>
        <tr><td>Floors</td><td><?php echo $p['floors']; ?></td></tr>
        <tr><td>Bedrooms</td><td><?php echo $p['bedrooms']; ?></td></tr>
        <tr><td>Bathrooms</td><td><?php echo $p['bathrooms']; ?></td></tr>
        <tr><td>Style</td><td><?php echo $p['style']; ?></td></tr>
      </table>
    </div>
    <div class="sheet__foot">
      This layout sheet is a sample for reference. For detailed floor plans and drawings, please contact
      <?php echo htmlspecialchars($site['name']); ?> — <?php echo htmlspecialchars($site['phone']); ?>.
    </div>
  </div>
</body>
</html>