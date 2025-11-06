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
  <style>
    :root{ --brand:#1a6b4a; }
    body{ background:#f5f7fa; }
    .sidebar{ width:240px; min-height:100vh; background:#123e2c; color:#fff; }
    .sidebar .brand{ font-weight:700; color:#fff; }
    .sidebar a{ color:#d9ede5; text-decoration:none; }
    .sidebar .nav-link{ border-radius:8px; padding:.5rem .75rem; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover{ background:#1a6b4a; color:#fff; }
    .topbar{ background:#1a6b4a; color:#fff; }
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
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/packages') ?>" href="<?= e($base) ?>/admin/packages">Paket</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/posts') ?>" href="<?= e($base) ?>/admin/posts">Posts</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/gallery-images') ?>" href="<?= e($base) ?>/admin/gallery-images">Galeri Gambar</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/gallery-videos') ?>" href="<?= e($base) ?>/admin/gallery-videos">Galeri Video</a></li>
      <li class="nav-item"><a class="nav-link <?= nav_active('/admin/schedules') ?>" href="<?= e($base) ?>/admin/schedules">Jadwal</a></li>
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
