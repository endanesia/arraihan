<?php
require_once __DIR__ . '/../inc/db.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $turnstile_response = $_POST['cf-turnstile-response'] ?? '';

    // Verify Cloudflare Turnstile
    $turnstile_verified = false;
    if ($turnstile_response) {
        $secret_key = '0x4AAAAAACAlxhMD2j8cBtPKCEaKNn-mXfz'; // Replace with your secret key
        $verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        
        $data = [
            'secret' => $secret_key,
            'response' => $turnstile_response,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = @file_get_contents($verify_url, false, $context);
        
        if ($result) {
            $result_data = json_decode($result, true);
            $turnstile_verified = $result_data['success'] ?? false;
        }
    }

    if (!$turnstile_verified) {
        $error = 'Verifikasi keamanan gagal. Silakan coba lagi.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = db()->prepare('SELECT id, username, password, name FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = (int)$row['id'];
                $_SESSION['admin_name'] = $row['name'];
        header('Location: dashboard');
                exit;
            }
        }
        $error = 'Login gagal. Periksa kembali username/password.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin | Umroh CMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <style>
    body { background:#f5f7fa; }
    .card { border:0; box-shadow:0 10px 30px rgba(0,0,0,.08); }
    .brand { color:#1a6b4a; font-weight:700; }
  </style>
  </head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="text-center mb-4">
          <h1 class="brand">Raihan Travelindo</h1>
          <div class="text-muted">Admin Login</div>
        </div>
        <div class="card p-4">
          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
          <?php endif; ?>
          <form method="post" autocomplete="off">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <div class="cf-turnstile" data-sitekey="0x4AAAAAACAl8S6dya4dFd3k"></div>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-success">Masuk</button>
            </div>ton type="submit" class="btn btn-success">Masuk</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
