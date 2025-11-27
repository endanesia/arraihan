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

    if ($username === '' || $password === '') {
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
            </div>
            <div class="mb-3">
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-success">Masuk</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
