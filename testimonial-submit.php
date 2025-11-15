<?php
require_once __DIR__ . '/inc/db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in response
ini_set('log_errors', 1);

// Verify Turnstile
function verifyTurnstile($token) {
    if (empty($token)) {
        return false;
    }
    
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
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
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
    if (empty($nama)) {
        echo json_encode(['success' => false, 'message' => 'Nama harus diisi!']);
        exit;
    }
    
    if (empty($judul)) {
        echo json_encode(['success' => false, 'message' => 'Judul harus diisi!']);
        exit;
    }
    
    if (empty($pesan)) {
        echo json_encode(['success' => false, 'message' => 'Pesan harus diisi!']);
        exit;
    }
    
    // Verify Turnstile
    if (!verifyTurnstile($turnstileToken)) {
        echo json_encode(['success' => false, 'message' => 'Verifikasi keamanan gagal. Silakan refresh halaman dan coba lagi.']);
        exit;
    }
    
    // Check database connection
    if (!db()) {
        echo json_encode(['success' => false, 'message' => 'Koneksi database gagal. Silakan hubungi administrator.']);
        exit;
    }
    
    // Insert to database
    try {
        $stmt = db()->prepare("INSERT INTO testimonials (nama, judul, pesan, is_approved) VALUES (?, ?, ?, 0)");
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . db()->error]);
            exit;
        }
        
        $stmt->bind_param('sss', $nama, $judul, $pesan);
        
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode([
                'success' => true, 
                'message' => 'Terima kasih! Testimonial Anda telah dikirim dan akan ditampilkan setelah disetujui oleh admin.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan testimonial: ' . $stmt->error]);
            $stmt->close();
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>