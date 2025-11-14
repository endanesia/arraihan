-- Tabel untuk testimonial
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    pesan TEXT NOT NULL,
    is_approved TINYINT(1) DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO testimonials (nama, judul, pesan, is_approved, is_featured) VALUES
('Ahmad Hidayat', 'Pengalaman Umroh yang Luar Biasa', 'Alhamdulillah, perjalanan umroh bersama Arraihan sangat berkesan. Pelayanan tour leader dan mutawwif sangat profesional. Hotel dekat dengan Masjidil Haram, membuat ibadah menjadi lebih khusyuk.', 1, 1),
('Siti Nurhaliza', 'Pelayanan Memuaskan', 'Terimakasih Arraihan Travelindo atas pelayanannya yang sangat memuaskan. Dari persiapan hingga kepulangan semua berjalan lancar. Sangat direkomendasikan!', 1, 1),
('Budi Santoso', 'Paket Umroh Terbaik', 'Ini pengalaman umroh kedua saya, dan dengan Arraihan jauh lebih baik. Harga terjangkau tapi kualitas pelayanan premium. Jazakumullah khairan.', 1, 1),
('Dewi Lestari', 'Sangat Berkesan', 'Umroh bersama keluarga sangat berkesan. Anak-anak juga senang karena semua kebutuhan terpenuhi dengan baik. Tour leader sabar dan ramah.', 1, 0),
('Hendra Wijaya', 'Recommended!', 'Arraihan Travelindo adalah pilihan terbaik untuk umroh. Jadwal jelas, tidak ada hidden cost, dan pembimbing sangat kompeten. Insya Allah akan umroh lagi dengan Arraihan.', 1, 0);
