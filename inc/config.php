<?php
// App configuration
// Environment detection - only use local config in development
$isLocal = ($_SERVER['SERVER_NAME'] ?? '') === 'localhost' || 
           ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' ||
           ($_SERVER['HTTP_HOST'] ?? '') === 'localhost:8080' || 
           strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
           strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
           php_sapi_name() === 'cli'; // Also detect CLI for local development

if ($isLocal) {
    $localConfigPath = __DIR__ . '/config-local.php';
    if (file_exists($localConfigPath)) {
        return require $localConfigPath;
    }
}

// Production configuration
return [
    'db' => [
        'host' => 'localhost',
        'user' => 'arraihan_users',
        'pass' => 'Hallo123123!@#', // ganti dengan password DB produksi
        'name' => 'arraihan_db',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => '', // root domain langsung
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/images/gallery'
    ]
];
