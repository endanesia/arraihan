<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

$base = rtrim($config['app']['base_url'] ?? '', '/');
$uri = $_SERVER['REQUEST_URI'] ?? '';
function nav_active($needle){
  global $uri; return (strpos($uri, $needle) !== false) ? 'active' : '';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Raihan Travelindo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root{ --brand:#1a6b4a; }
    body{ background:#f5f7fa; }
    .sidebar{ width:240px; min-height:100vh; background:#123e2c; color:#fff; }
    .sidebar .brand{ font-weight:700; color:#fff; }
    .sidebar a{ color:#d9ede5; text-decoration:none; }
    .sidebar .nav-link{ border-radius:8px; padding:.5rem .75rem; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover{ background:#1a6b4a; color:#fff; }
    .topbar{ background:#1a6b4a; color:#fff; }
    .submenu{ background:#0d2818; border-radius:5px; }
    .submenu .nav-link{ font-size:0.9rem; padding:.4rem .6rem; }
    .submenu .nav-link:hover{ background:#1a6b4a; }
    .submenu-toggle{ transition: transform 0.2s ease; }
    .submenu-toggle.rotated{ transform: rotate(180deg); }
  </style>
</head>
<body>
<div class="d-flex">
  <aside class="sidebar p-3">
    <div class="d-flex align-items-center mb-3">
      <a class="brand h5 mb-0" href="<?= e($base) ?>/admin/dashboard">Raihan Admin</a>
    </div>
    <hr class="border-light">
    <ul class="nav flex-column gap-1">
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/dashboard') ?>" href="<?= e($base) ?>/admin/dashboard">Dashboard</a></li>
      
      <!-- Homepage Menu with Submenu -->
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?= nav_active('/admin/hero') || nav_active('/admin/greeting') || nav_active('/admin/keunggulan') || nav_active('/admin/about') ? 'active' : '' ?>" 
           href="#" onclick="toggleSubmenu('homepage-menu')">
          <span><i class="fas fa-home me-2"></i>Homepage</span>
          <i class="fas fa-chevron-down submenu-toggle" id="homepage-arrow"></i>
        </a>
        <ul class="nav flex-column ps-3 mt-1 submenu" id="homepage-menu" style="display: <?= nav_active('/admin/hero') || nav_active('/admin/greeting') || nav_active('/admin/keunggulan') || nav_active('/admin/about') ? 'block' : 'none' ?>;">
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/hero') ?>" href="<?= e($base) ?>/admin/hero">
              <i class="fas fa-image me-2"></i>Hero
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/greeting') ?>" href="<?= e($base) ?>/admin/greeting">
              <i class="fas fa-handshake me-2"></i>Greeting
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/keunggulan') ?>" href="<?= e($base) ?>/admin/keunggulan">
              <i class="fas fa-star me-2"></i>Keunggulan
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/about') ?>" href="<?= e($base) ?>/admin/about">
              <i class="fas fa-info-circle me-2"></i>Tentang Kami
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/packages') ?>" href="<?= e($base) ?>/admin/packages">Paket Travel</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/posts') ?>" href="<?= e($base) ?>/admin/posts">Artikel & Berita</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/gallery-images') ?>" href="<?= e($base) ?>/admin/gallery-images">Galeri Gambar</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/gallery-videos') ?>" href="<?= e($base) ?>/admin/gallery-videos">Galeri Video</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/schedules') ?>" href="<?= e($base) ?>/admin/schedules">Jadwal Keberangkatan</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/partners') ?>" href="<?= e($base) ?>/admin/partners">Partner</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/social-links') ?>" href="<?= e($base) ?>/admin/social-links">Pengaturan Situs</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/upgrade') ?>" href="<?= e($base) ?>/admin/upgrade">Upgrade DB</a></li>
    </ul>
  </aside>
  <div class="flex-grow-1">
    <div class="topbar px-3 py-2 d-flex align-items-center justify-content-between">
      <div>Panel Administrasi</div>
      <div class="d-flex align-items-center gap-2">
        <span class="small">Halo, <?= e($_SESSION['admin_name'] ?? 'Admin'); ?></span>
        <a class="btn btn-sm btn-light" href="<?= e($base) ?>/admin/logout">Logout</a>
      </div>
    </div>
    <main class="p-3">
