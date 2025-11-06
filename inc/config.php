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
        'base_url' => '', // root domain langsung
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/images/gallery'
    ]
];
