<?php
/**
 * Admin panel — shared header + sidebar + topbar.
 * Expects: $user (authenticated admin array), $active (sidebar key).
 */
declare(strict_types=1);

$active = $active ?? '';
$unread = Notification::unreadCount('admin', (int)$user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($title ?? 'Dashboard'); ?> — Seraph Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="/css/panel/base.css">
  <link rel="stylesheet" href="/css/panel/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="admin-shell">

  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
      <img src="/images/seraph-logo@204w.webp" alt="Seraph Build" height="34">
    </div>

    <nav class="sidebar__nav">
      <div class="sidebar__label">Overview</div>
      <a class="sidebar__link <?php echo $active === 'dashboard' ? 'active' : ''; ?>" href="/admin/">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>

      <div class="sidebar__label">Projects</div>
      <a class="sidebar__link <?php echo $active === 'projects' ? 'active' : ''; ?>" href="/admin/projects">
        <i class="fa-solid fa-building"></i> All Projects
      </a>
      <a class="sidebar__link <?php echo $active === 'project_new' ? 'active' : ''; ?>" href="/admin/projects/create">
        <i class="fa-solid fa-circle-plus"></i> New Project
      </a>

      <div class="sidebar__label">People</div>
      <a class="sidebar__link <?php echo $active === 'clients' ? 'active' : ''; ?>" href="/admin/clients">
        <i class="fa-solid fa-users"></i> Clients
      </a>
      <?php if (Auth::isSuper($user)): ?>
      <a class="sidebar__link <?php echo $active === 'admins' ? 'active' : ''; ?>" href="/admin/admins">
        <i class="fa-solid fa-user-shield"></i> Admins
      </a>
      <?php endif; ?>
      <a class="sidebar__link <?php echo $active === 'audit' ? 'active' : ''; ?>" href="/admin/audit">
        <i class="fa-solid fa-clipboard-list"></i> Activity Log
      </a>
    </nav>

    <div class="sidebar__footer">
      <div class="small">Seraph Build Construction<br>&copy; <?php echo date('Y'); ?></div>
    </div>
  </aside>

  <div class="admin-main">
    <header class="topbar">
      <div class="flex">
        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="topbar__title"><?php echo e($title ?? 'Dashboard'); ?></div>
      </div>

      <div class="topbar__actions">
        <form class="topbar__search" action="/admin/projects" method="GET">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" name="search" placeholder="Search projects..." value="<?php echo e($_GET['search'] ?? ''); ?>">
        </form>

        <div class="notif" id="notifWrap">
          <button class="notif__bell" id="notifBell" aria-label="Notifications">
            <i class="fa-solid fa-bell"></i>
            <?php if ($unread > 0): ?>
              <span class="notif__count" id="notifCount"><?php echo $unread; ?></span>
            <?php endif; ?>
          </button>
          <div class="notif__dropdown" id="notifDropdown">
            <div id="notifList">Loading...</div>
          </div>
        </div>

        <div class="user-menu" id="userMenuWrap">
          <button class="user-menu__btn" id="userMenuBtn">
            <span class="user-menu__avatar"><?php echo e(strtoupper(substr($user['full_name'], 0, 1))); ?></span>
            <span class="small" style="display:none"><?php echo e($user['full_name']); ?></span>
            <i class="fa-solid fa-chevron-down small"></i>
          </button>
          <div class="user-menu__dropdown" id="userMenuDropdown">
            <div class="user-menu__info">
              <div class="user-menu__name"><?php echo e($user['full_name']); ?></div>
              <div class="user-menu__email"><?php echo e($user['email']); ?></div>
            </div>
            <a href="/admin/"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="/" target="_blank"><i class="fa-solid fa-globe"></i> View Website</a>
            <a href="/admin/logout" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
          </div>
        </div>
      </div>
    </header>

    <main class="admin-content">
