<?php
/**
 * Dev utility: generate responsive WebP variants for the public site.
 *
 *   php scripts/generate-webp.php          # only missing variants
 *   php scripts/generate-webp.php --force  # regenerate everything
 *
 * Outputs (never upscales; quality 82):
 *   images/materials/<name>.png   ->  <name>@840w.webp, <name>@1264w.webp
 *   images/<kitchen>@1112w.webp   ->  <kitchen>@640w.webp
 */

$root = dirname(__DIR__);
$force = in_array('--force', $argv ?? [], true);

$jobs = [];

foreach (glob($root . '/images/materials/*.png') as $png) {
    $base = preg_replace('/\.png$/', '', $png);
    $jobs[] = [$png, $base . '@840w.webp', 840];
    $jobs[] = [$png, $base . '@1264w.webp', 1264];
}

foreach (['elevation', 'livingroom', 'bedroom', 'modularkitchen', 'toilet'] as $name) {
    $src = $root . '/images/' . $name . '@1112w.webp';
    if (file_exists($src)) {
        $jobs[] = [$src, $root . '/images/' . $name . '@640w.webp', 640];
    }
}

function seraph_convert(string $src, string $dst, int $width): string
{
    $data = file_get_contents($src);
    $im = $data !== false ? @imagecreatefromstring($data) : false;
    if (!$im) {
        return "SKIP (decode failed): $src";
    }
    $w = imagesx($im);
    $h = imagesy($im);
    if ($width > $w) {
        imagedestroy($im);
        return "SKIP (not wider): " . basename($dst);
    }
    $nh = max(1, (int) round($h * $width / $w));
    $out = imagecreatetruecolor($width, $nh);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagecopyresampled($out, $im, 0, 0, 0, 0, $width, $nh, $w, $h);
    imagedestroy($im);
    imagewebp($out, $dst, 82);
    imagedestroy($out);
    return sprintf(
        "OK  %s -> %dx%d, %dKB",
        basename($dst),
        $width,
        $nh,
        (int) round(filesize($dst) / 1024)
    );
}

$made = 0;
$skipped = 0;
foreach ($jobs as [$src, $dst, $width]) {
    if (!file_exists($src)) {
        echo "MISS src: $src\n";
        continue;
    }
    if (file_exists($dst) && !$force) {
        $skipped++;
        continue;
    }
    echo seraph_convert($src, $dst, $width) . "\n";
    $made++;
}
echo "done: $made generated, $skipped already present\n";
