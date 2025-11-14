-- Tabel untuk data Mutawwif dan Tour Leader
CREATE TABLE IF NOT EXISTS mutawwif (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jabatan VARCHAR(255) NOT NULL DEFAULT 'Mutawwif Indonesia',
    foto VARCHAR(255) NULL,
    urutan INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data sesuai gambar
INSERT INTO mutawwif (nama, jabatan, foto, urutan, is_active) VALUES
('Uztad Mochammad Munir Djamil', 'Mutawwif Indonesia', NULL, 1, 1),
('Ustad Achmad Suudi Bin Sulaiman', 'Mutawwif Indonesia', NULL, 2, 1),
('Ustad Muhammad Yazid Abdul Malik', 'Mutawwif Indonesia', NULL, 3, 1),
('Ustad Muzakki Munawi', 'Mutawwif Indonesia', NULL, 4, 1);
