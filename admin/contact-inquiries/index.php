<?php
/**
 * Folder entry point — includes the flat list page (no redirect).
 * Prevents ERR_TOO_MANY_REDIRECTS when /admin/contact-inquiries/ is requested.
 */
declare(strict_types=1);

require dirname(__DIR__) . '/contact-inquiries.php';
