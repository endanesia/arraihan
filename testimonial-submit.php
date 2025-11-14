<?php
require_once __DIR__ . '/inc/db.php';

header('Content-Type: application/json');

// Verify Turnstile
function verifyTurnstile($token) {
    $secret = '0x4AAAAAACAl8Xxd_jfNPXTxmVNSm-QHYNo';
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    
    $data = [
        'secret' => $secret,
        'response' => $token
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        return false;
    }
    
    $resultJson = json_decode($result, true);
    return isset($resultJson['success']) && $resultJson['success'] === true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    
    // Validate
    if (empty($nama) || empty($judul) || empty($pesan)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi!']);
        exit;
    }
    
    // Verify Turnstile
    if (!verifyTurnstile($turnstileToken)) {
        echo json_encode(['success' => false, 'message' => 'Verifikasi keamanan gagal. Silakan coba lagi.']);
        exit;
    }
    
    // Insert to database
    try {
        $stmt = db()->prepare("INSERT INTO testimonials (nama, judul, pesan, is_approved) VALUES (?, ?, ?, 0)");
        $stmt->bind_param('sss', $nama, $judul, $pesan);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Terima kasih! Testimonial Anda telah dikirim dan akan ditampilkan setelah disetujui oleh admin.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan testimonial. Silakan coba lagi.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
