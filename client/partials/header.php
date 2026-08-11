<?php
/**
 * Client portal — shared header + topbar.
 * Expects: $user (authenticated client array), $title.
 */
declare(strict_types=1);

$title = $title ?? 'My Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($title); ?> — Client Portal</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <link rel="stylesheet" href="/css/panel/base.css">
  <link rel="stylesheet" href="/css/panel/client.css">
  <link rel="stylesheet" href="/css/panel/fa.css">
  <noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..600;1,9..144,400..600&family=IBM+Plex+Mono:wght@400;500&family=Public+Sans:ital,wght@0,400..700;1,400..700&display=swap">
  </noscript>
</head>
<body>
<header class="client-topbar">
  <div class="client-topbar__brand">
    <img src="/images/seraph-logo@204w.webp" alt="Seraph Build" width="128" height="30">
    <span>Client Portal</span>
  </div>
  <div class="client-topbar__right">
    <span class="live-dot" id="liveIndicator">Live</span>
    <div class="user-menu" id="userMenuWrap">
      <button class="user-menu__btn" id="userMenuBtn">
        <span class="user-menu__avatar"><?php echo e(strtoupper(substr($user['contact_person'], 0, 1))); ?></span>
        <span class="small"><?php echo e($user['contact_person']); ?></span>
        <i class="fa-solid fa-chevron-down small"></i>
      </button>
      <div class="user-menu__dropdown" id="userMenuDropdown">
        <div class="user-menu__info">
          <div class="user-menu__name"><?php echo e($user['contact_person']); ?></div>
          <div class="user-menu__email"><?php echo e($user['email']); ?></div>
        </div>
        <a href="/client/"><i class="fa-solid fa-house"></i> My Projects</a>
        <a href="/" target="_blank"><i class="fa-solid fa-globe"></i> View Website</a>
        <form method="post" action="/client/logout" class="logout-form">
          <?php echo CSRF::field(); ?>
          <button type="submit" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</button>
        </form>
      </div>
    </div>
  </div>
</header>

<main class="client-content">