<?php
session_start();
require_once __DIR__ . '/inc/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
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
        $_SESSION['testimonial_message'] = 'Nama harus diisi!';
        $_SESSION['testimonial_type'] = 'danger';
        header('Location: testimonial.php');
        exit;
    }
    
    if (empty($judul)) {
        $_SESSION['testimonial_message'] = 'Judul harus diisi!';
        $_SESSION['testimonial_type'] = 'danger';
        header('Location: testimonial.php');
        exit;
    }
    
    if (empty($pesan)) {
        $_SESSION['testimonial_message'] = 'Pesan harus diisi!';
        $_SESSION['testimonial_type'] = 'danger';
        header('Location: testimonial.php');
        exit;
    }
    
    // Verify Turnstile
    if (!verifyTurnstile($turnstileToken)) {
        $_SESSION['testimonial_message'] = 'Verifikasi keamanan gagal. Silakan coba lagi.';
        $_SESSION['testimonial_type'] = 'danger';
        header('Location: testimonial.php');
        exit;
    }
    
    // Check database connection
    if (!db()) {
        $_SESSION['testimonial_message'] = 'Koneksi database gagal. Silakan hubungi administrator.';
        $_SESSION['testimonial_type'] = 'danger';
        header('Location: testimonial.php');
        exit;
    }
    
    // Insert to database
    try {
        $stmt = db()->prepare("INSERT INTO testimonials (nama, judul, pesan, is_approved) VALUES (?, ?, ?, 0)");
        
        if (!$stmt) {
            $_SESSION['testimonial_message'] = 'Error preparing statement: ' . db()->error;
            $_SESSION['testimonial_type'] = 'danger';
            header('Location: testimonial.php');
            exit;
        }
        
        $stmt->bind_param('sss', $nama, $judul, $pesan);
        
        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['testimonial_message'] = 'Terima kasih! Testimonial Anda telah dikirim dan akan ditampilkan setelah disetujui oleh admin.';
            $_SESSION['testimonial_type'] = 'success';
            header('Location: testimonial.php');
            exit;
        } else {
            $_SESSION['testimonial_message'] = 'Gagal menyimpan testimonial: ' . $stmt->error;
            $_SESSION['testimonial_type'] = 'danger';
            $stmt->close();
            header('Location: testimonial.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['testimonial_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        $_SESSION['testimonial_type'] = 'danger';
        header('Location: testimonial.php');
        exit;
    }
} else {
    $_SESSION['testimonial_message'] = 'Invalid request method';
    $_SESSION['testimonial_type'] = 'danger';
    header('Location: testimonial.php');
    exit;
}
?>