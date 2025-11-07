-- SQL untuk membuat tabel settings jika belum ada
-- Jalankan query ini di database Anda

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default hero settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('hero_title', 'Perjalanan Suci Berkualitas, Biaya Bersahabat'),
('hero_subtitle', 'Jangan biarkan biaya menunda niat suci Anda. Paket Umroh terjangkau dengan tetap berkualitas layanan terbaik, mencakup akomodasi dan bimbingan yang profesional. Wujudkan ibadah khusyuk dan nyaman Anda, karena Umroh berkualitas kini bisa diakses oleh semua.'),
('hero_button_text', 'Lihat Paket Umroh'),
('hero_stat1_text', '24 Januri 2026'),
('hero_stat1_desc', 'Jadwal Berangkat'),
('hero_stat2_text', 'Program Pembiayaan'),
('hero_stat2_desc', 'Pembiayaan dana talangan Umrah'),
('hero_background', '')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);