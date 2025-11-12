<?php
// Local development configuration
return [
    'db' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'arraihan_db',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => 'http://localhost/dev',
        'uploads_dir' => __DIR__ . '/../images/gallery',
        'uploads_url' => '/dev/images/gallery'
    ]
];