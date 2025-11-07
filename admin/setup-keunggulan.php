<?php
// Script to create keunggulan table
require_once __DIR__ . '/../inc/db.php';

try {
    $db = db();
    
    // Create keunggulan table
    $sql = "CREATE TABLE IF NOT EXISTS keunggulan (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        icon VARCHAR(100) NOT NULL,
        order_num INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->query($sql);
    
    // Check if table has data
    $result = $db->query("SELECT COUNT(*) as count FROM keunggulan");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        // Insert default keunggulan data
        $default_keunggulan = [
            [
                'title' => 'Mutu Pelayanan',
                'description' => 'Telah lulus uji dan bersertifikasi memenuhi kualifikasi SNI ISO 9001:2015 QMS',
                'icon' => 'fas fa-award',
                'order_num' => 1
            ],
            [
                'title' => 'Jadwal Pasti',
                'description' => 'Semua paket sudah tertera tanggal berangkat, nomor pesawat & itinerary perjalanan yang jelas',
                'icon' => 'fas fa-calendar-check',
                'order_num' => 2
            ],
            [
                'title' => 'Direct Flight',
                'description' => 'Penerbangan langsung tanpa transit sehingga jamaah tidak perlu menunggu dan lebih nyaman',
                'icon' => 'fas fa-plane',
                'order_num' => 3
            ],
            [
                'title' => 'Hotel Strategis',
                'description' => 'Hotel kami di ring 1 dekat masjid, baik di Masjidil Haram maupun di Masjid Nabawi',
                'icon' => 'fas fa-hotel',
                'order_num' => 4
            ],
            [
                'title' => 'Muthawif Profesional',
                'description' => 'Pembimbing ibadah yang berpengalaman dan bermukim di Mekkah untuk melayani jamaah',
                'icon' => 'fas fa-user-tie',
                'order_num' => 5
            ],
            [
                'title' => 'Wireless Headset',
                'description' => 'Asistensi menggunakan alat bantu wireless headset untuk memudahkan thawaf dan sa\'i',
                'icon' => 'fas fa-headset',
                'order_num' => 6
            ]
        ];
        
        $stmt = $db->prepare("INSERT INTO keunggulan (title, description, icon, order_num, is_active) VALUES (?, ?, ?, ?, 1)");
        
        foreach ($default_keunggulan as $item) {
            $stmt->bind_param('sssi', $item['title'], $item['description'], $item['icon'], $item['order_num']);
            $stmt->execute();
        }
        
        echo "Tabel keunggulan berhasil dibuat dan diisi dengan data default!\n";
    } else {
        echo "Tabel keunggulan sudah ada dengan $count data.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>