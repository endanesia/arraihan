<?php
// App configuration
return [
    'db' => [
        'host' => 'localhost',
        'user' => 'arraihan_users',
        'pass' => 'Hallo123123!@#', // ganti dengan password DB produksi
        'name' => 'arraihan_db',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => '/dev', // sesuai path di production
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/dev/images/gallery'
    ]
];
