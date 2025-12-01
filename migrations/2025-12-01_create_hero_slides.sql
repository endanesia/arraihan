-- Migration: Create hero_slides table for slideshow functionality
-- Date: 2025-12-01
-- Description: Create table to store multiple hero slides for slideshow

CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT NOT NULL,
    button_text VARCHAR(100) DEFAULT 'Lihat Program Umroh',
    button_link VARCHAR(255) DEFAULT '#paket',
    stat1_text VARCHAR(100) DEFAULT '',
    stat1_desc VARCHAR(100) DEFAULT '',
    stat2_text VARCHAR(100) DEFAULT '',
    stat2_desc VARCHAR(100) DEFAULT '',
    background_image VARCHAR(500) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default hero slide (migrating from settings)
INSERT IGNORE INTO hero_slides (
    title, 
    subtitle, 
    button_text, 
    stat1_text, 
    stat1_desc, 
    stat2_text, 
    stat2_desc, 
    background_image,
    sort_order
) VALUES (
    'Perjalanan Suci Berkualitas, Biaya Bersahabat',
    'Jangan biarkan biaya menunda niat suci Anda. Program Umroh terjangkau dengan tetap berkualitas layanan terbaik, mencakup akomodasi dan bimbingan yang profesional. Wujudkan ibadah khusyuk dan nyaman Anda, karena Umroh berkualitas kini bisa diakses oleh semua.',
    'Lihat Program Umroh',
    '24 Januri 2026',
    'Jadwal Berangkat',
    'Program Pembiayaan',
    'Pembiayaan dana talangan Umrah',
    '/images/hero-bg.jpg',
    1
);