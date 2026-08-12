<?php
/**
 * Legacy folder URL shim — redirects to the flat admin list page.
 * Keeps /admin/contact-inquiries/ working if this folder still exists on the server.
 */
declare(strict_types=1);

header('Location: /admin/contact-inquiries', true, 301);
exit;
