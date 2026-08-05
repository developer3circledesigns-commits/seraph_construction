<?php
/**
 * Section partials are included from the sections/ directory.
 * This file provides a small helper to render each section.
 */
require_once __DIR__ . '/../config/site.php';
$site = require __DIR__ . '/../config/site.php';

/**
 * Render a section partial.
 */
function render_section(string $name, array $site): void
{
    include __DIR__ . '/sections/' . $name . '.php';
}
