<?php
// Production configuration
return [
    'db' => [
        'host' => 'localhost', // atau sesuai hosting provider
        'user' => 'username_db_production',
        'pass' => 'password_db_production',
        'name' => 'nama_database_production',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => '', // root path untuk production
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/images/gallery'
    ]
];