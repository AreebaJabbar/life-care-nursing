<?php
require_once __DIR__ . '/../config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate credentials (default: admin / admin123)
    if ($username === ADMIN_USERNAME && ($password === 'admin123' || password_verify($password, ADMIN_PASSWORD_HASH))) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid Username or Password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — LifeCare Nursing</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@700&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --teal-900: #0E3B36;
            --brand-navy: #03357A;
            --brand-teal: #029491;
        }
        body { font-family: 'Manrope', sans-serif; background: #0E3B36; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-card { width: 100%; max-width: 420px; background: #ffffff; border-radius: 16px; box-shadow: 0 20px 45px rgba(0,0,0,0.3); overflow: hidden; }
        .login-header { background: var(--brand-navy); color: #fff; padding: 2.5rem 2rem 2rem; text-align: center; }
        .login-header img { height: 50px; background: #fff; padding: 5px; border-radius: 8px; margin-bottom: 0.8rem; }
        .login-header h4 { font-family: 'Fraunces', serif; margin: 0; font-size: 1.5rem; }
        .login-header p { color: #B0C4DE; font-size: 0.85rem; margin-top: 0.25rem; }
        .login-body { padding: 2rem; }
        .btn-care { background: var(--brand-teal); color: #fff; font-weight: 700; border: none; padding: 0.85rem; border-radius: 8px; width: 100%; transition: 0.25s; }
        .btn-care:hover { background: var(--brand-navy); color: #fff; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="../assets/logo.png" alt="LifeCare Logo">
        <h4>LifeCare Admin Login</h4>
        <p>Manage Doctors, Staff & Team Cards</p>
    </div>
    <div class="login-body">
        <?php if ($error): ?>
            <div class="alert alert-danger font-weight-bold" style="font-size: 0.88rem;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem; letter-spacing: 0.05em;">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill text-muted"></i></span>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" value="admin" required autofocus style="font-size: 0.95rem;">
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem; letter-spacing: 0.05em;">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="••••••••" required style="font-size: 0.95rem;">
                </div>
                <div class="form-text text-muted" style="font-size: 0.78rem;">Default Password: <strong>admin123</strong></div>
            </div>
            <button type="submit" class="btn-care">
                <i class="bi bi-box-arrow-in-right me-1"></i> Log In to Dashboard
            </button>
        </form>
    </div>
</div>

</body>
</html>
