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
    body{ background:#f5f7fa; margin:0; padding:0; }
    .d-flex { display: flex !important; }
    .sidebar{ 
      width: 280px !important; 
      min-width: 280px !important;
      max-width: 280px !important;
      flex-shrink: 0 !important;
      min-height: 100vh; 
      background: #123e2c; 
      color: #fff;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 1030;
      overflow-y: auto;
      padding: 1rem !important;
    }
    .sidebar .brand{ font-weight:700; color:#fff; font-size: 1.25rem; }
    .sidebar a{ color:#d9ede5; text-decoration:none; }
    .sidebar .nav-link{ 
      border-radius:8px; 
      padding:.75rem 1rem;
      font-size: 0.95rem;
      white-space: nowrap;
      display: flex;
      align-items: center;
      transition: all 0.2s ease;
      position: relative;
    }
    .sidebar .nav-link.active, .sidebar .nav-link:hover{ 
      background:#1a6b4a; 
      color:#fff;
      transform: translateX(5px);
    }
    .sidebar .nav-link.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 60%;
      background: #fff;
      border-radius: 0 2px 2px 0;
    }
    .sidebar .nav-link i { 
      width: 20px; 
      text-align: center; 
      margin-right: 0.75rem;
      flex-shrink: 0;
    }
    .main-content {
      margin-left: 280px !important;
      width: calc(100% - 280px) !important;
      min-height: 100vh;
    }
    .topbar{ background:#1a6b4a; color:#fff; }
    .submenu{ background:#0d2818; border-radius:5px; margin-top: 0.5rem; }
    .submenu .nav-link{ 
      font-size:0.85rem; 
      padding:.5rem 1rem .5rem 2.5rem;
      margin-left: 0;
    }
    .submenu .nav-link:hover{ background:#1a6b4a; }
    .submenu-toggle{ transition: transform 0.2s ease; }
    .submenu-toggle.rotated{ transform: rotate(180deg); }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }
      .sidebar.show {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0 !important;
        width: 100% !important;
      }
    }
  </style>
</head>
<body>
<div class="d-flex">
  <aside class="sidebar">
    <div class="d-flex align-items-center mb-4">
      <a class="brand text-decoration-none d-flex align-items-center" href="<?= e($base) ?>/admin/dashboard">
        <img src="<?= e($base) ?>/images/logo.png" alt="Ar Raihan" style="height: 35px; margin-right: 10px;">
        <span>Ar Raihan</span>
      </a>
    </div>
    <hr class="border-light opacity-25">
    <ul class="nav flex-column gap-1">
      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/dashboard') ?>" href="<?= e($base) ?>/admin/dashboard">
          <i class="fas fa-tachometer-alt"></i>Dashboard
        </a>
      </li>
      
      <!-- Homepage Menu with Submenu -->
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?= nav_active('/admin/hero') || nav_active('/admin/greeting') || nav_active('/admin/keunggulan') || nav_active('/admin/about') || nav_active('/admin/partners') ? 'active' : '' ?>" 
           href="#" onclick="toggleSubmenu('homepage-menu')">
          <span><i class="fas fa-home"></i>Homepage</span>
          <i class="fas fa-chevron-down submenu-toggle" id="homepage-arrow"></i>
        </a>
        <ul class="nav flex-column submenu" id="homepage-menu" style="display: <?= nav_active('/admin/hero') || nav_active('/admin/greeting') || nav_active('/admin/keunggulan') || nav_active('/admin/about') ? 'block' : 'none' ?>;">
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/hero') ?>" href="<?= e($base) ?>/admin/hero">
              <i class="fas fa-image"></i>Hero Section
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/greeting') ?>" href="<?= e($base) ?>/admin/greeting">
              <i class="fas fa-handshake"></i>Greeting
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/keunggulan') ?>" href="<?= e($base) ?>/admin/keunggulan">
              <i class="fas fa-star"></i>Keunggulan
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/about') ?>" href="<?= e($base) ?>/admin/about">
              <i class="fas fa-info-circle"></i>Tentang Kami
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= nav_active('/admin/partners') ?>" href="<?= e($base) ?>/admin/partners">
              <i class="fas fa-handshake"></i>Partner
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/packages') ?>" href="<?= e($base) ?>/admin/packages">
          <i class="fas fa-suitcase"></i>Paket Haji & Umroh
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/posts') ?>" href="<?= e($base) ?>/admin/posts">
          <i class="fas fa-newspaper"></i>Artikel & Berita
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/gallery-images') ?>" href="<?= e($base) ?>/admin/gallery-images">
          <i class="fas fa-images"></i>Galeri Gambar
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/gallery-videos') ?>" href="<?= e($base) ?>/admin/gallery-videos">
          <i class="fas fa-video"></i>Galeri Video
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/schedules') ?>" href="<?= e($base) ?>/admin/schedules">
          <i class="fas fa-calendar-alt"></i>Jadwal Keberangkatan
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= nav_active('/admin/social-links') ?>" href="<?= e($base) ?>/admin/social-links">
          <i class="fas fa-cog"></i>Pengaturan Situs
        </a>
      </li>
      
    </ul>
  </aside>
  <div class="main-content">
    <div class="topbar px-4 py-3 d-flex align-items-center justify-content-between shadow-sm">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebar-toggle">
          <i class="fas fa-bars"></i>
        </button>
        <h6 class="mb-0">Panel Administrasi</h6>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="small">Halo, <?= e($_SESSION['admin_name'] ?? 'Admin'); ?> 👋</span>
        <a class="btn btn-sm btn-light" href="<?= e($base) ?>/admin/logout">
          <i class="fas fa-sign-out-alt me-1"></i>Logout
        </a>
      </div>
    </div>
    <main class="p-4">
