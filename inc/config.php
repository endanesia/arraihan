<?php
// App configuration
return [
    'db' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '', // ganti dengan password DB produksi
        'name' => 'umroh_cms',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => '', // root untuk production
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/images/gallery'
    ]
];
