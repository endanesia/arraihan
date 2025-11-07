<?php
// Local development configuration
return [
    'db' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'arraihan_dev',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => 'http://localhost:8080',
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/images/gallery'
    ]
];