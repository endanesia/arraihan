<?php
require_once __DIR__ . '/inc/db.php';

header('Content-Type: application/json');

// Fungsi untuk validasi Turnstile
function verifyTurnstile($token) {
    $secret = '0x4AAAAAACAl8Xxd_jfNPXTxmVNSm-QHYNo';
    
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => $secret,
        'response' => $token
    ]));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return isset($result['success']) && $result['success'] === true;
}

// Validasi method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ambil data dari POST
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$wa = isset($_POST['wa']) ? trim($_POST['wa']) : '';
$pesan = isset($_POST['pesan']) ? trim($_POST['pesan']) : '';
$turnstileToken = isset($_POST['cf-turnstile-response']) ? $_POST['cf-turnstile-response'] : '';

// Validasi input
if (empty($nama) || empty($email) || empty($wa) || empty($pesan)) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
    exit;
}

// Validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email tidak valid']);
    exit;
}

// Validasi Turnstile
if (empty($turnstileToken)) {
    echo json_encode(['success' => false, 'message' => 'Captcha tidak valid']);
    exit;
}

if (!verifyTurnstile($turnstileToken)) {
    echo json_encode(['success' => false, 'message' => 'Verifikasi captcha gagal']);
    exit;
}

// Simpan ke database
try {
    $db = db();
    if (!$db) {
        throw new Exception('Koneksi database gagal');
    }
    
    $stmt = $db->prepare("INSERT INTO contact_messages (nama, email, wa, pesan) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception('Prepare statement gagal: ' . $db->error);
    }
    
    $stmt->bind_param('ssss', $nama, $email, $wa, $pesan);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.'
        ]);
    } else {
        throw new Exception('Gagal menyimpan data: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
