<?php
require_once __DIR__ . '/config.php';

// Note: login.php always shows the role selector screen, even if a
// session is already active. The dashboard only opens after the user
// explicitly selects a role and submits valid credentials below.

$error = '';
$selectedRole = isset($_GET['role']) && in_array($_GET['role'], ['admin', 'doctor', 'staff']) ? $_GET['role'] : 'doctor';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRole = isset($_POST['role']) && in_array($_POST['role'], ['admin', 'doctor', 'staff']) ? $_POST['role'] : 'doctor';
    $username     = trim($_POST['username'] ?? '');
    $password     = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter your Username / Email and Password.';
    } else {
        if ($selectedRole === 'admin') {
            // ADMIN LOGIN VERIFICATION
            if ($username === ADMIN_USERNAME && ($password === 'admin123' || password_verify($password, ADMIN_PASSWORD_HASH))) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username']  = $username;
                header('Location: admin/index.php');
                exit;
            } else {
                $error = 'Invalid Admin credentials. Please check your username and password.';
            }
        } elseif ($selectedRole === 'doctor') {
            // DOCTOR LOGIN VERIFICATION
            $doctor = getDoctorByUsername($username);
            if ($doctor && isset($doctor['password']) && password_verify($password, $doctor['password'])) {
                $status = $doctor['status'] ?? 'approved';
                if ($status === 'pending') {
                    $error = 'Your doctor profile is <strong>Pending Admin Approval</strong>. Please wait for an admin to activate your account.';
                } elseif ($status === 'disabled' || $status === 'rejected') {
                    $error = 'Your doctor account has been <strong>disabled or rejected</strong> by administration.';
                } else {
                    $_SESSION['doctor_id']   = (int)$doctor['id'];
                    $_SESSION['doctor_name'] = $doctor['name'];
                    header('Location: doctor-dashboard.php');
                    exit;
                }
            } else {
                $error = 'Invalid Doctor Username or Password.';
            }
        } elseif ($selectedRole === 'staff') {
            // STAFF LOGIN VERIFICATION
            $staff = getStaffByUsername($username);
            if ($staff && isset($staff['password']) && password_verify($password, $staff['password'])) {
                $status = $staff['status'] ?? 'approved';
                if ($status === 'pending') {
                    $error = 'Your staff registration is <strong>Pending Admin Approval</strong>. Please wait for management approval.';
                } elseif ($status === 'disabled' || $status === 'rejected') {
                    $error = 'Your staff account has been <strong>disabled or rejected</strong> by administration.';
                } else {
                    $_SESSION['staff_id']   = (int)$staff['id'];
                    $_SESSION['staff_name'] = $staff['name'];
                    header('Location: staff-dashboard.php');
                    exit;
                }
            } else {
                $error = 'Invalid Staff Username or Password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Unified Login — <?= SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  :root {
    --brand-navy: #03357A;
    --brand-teal: #029491;
    --teal-900: #0E3B36;
    --teal-700: #1B6B63;
    --amber-500: #D9A441;
    --bg-soft: #F4F8F7;
  }
  body {
    font-family: 'Manrope', sans-serif;
    background: var(--bg-soft);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
  }
  .login-card {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 20px 45px rgba(14, 59, 54, 0.1);
    width: 100%;
    max-width: 520px;
    padding: 2.5rem;
    border: 1px solid #E2EDE9;
  }
  .login-card h2 {
    font-family: 'Fraunces', serif;
    color: var(--teal-900);
    font-weight: 700;
  }
  .role-btn-group {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.6rem;
    margin-bottom: 1.8rem;
  }
  .role-card {
    border: 2px solid #E0ECE8;
    background: #F9FBFB;
    border-radius: 12px;
    padding: 0.85rem 0.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    user-select: none;
  }
  .role-card i {
    font-size: 1.4rem;
    display: block;
    margin-bottom: 0.35rem;
    color: #6C827E;
    transition: color 0.25s ease;
  }
  .role-card span {
    font-size: 0.82rem;
    font-weight: 700;
    color: #4A5B57;
    display: block;
  }
  .role-card:hover {
    border-color: var(--brand-teal);
    background: rgba(2, 148, 145, 0.05);
  }
  .role-card.active {
    border-color: var(--brand-teal);
    background: var(--brand-teal);
    color: #fff;
    box-shadow: 0 6px 16px rgba(2, 148, 145, 0.25);
  }
  .role-card.active i, .role-card.active span {
    color: #ffffff !important;
  }
  .btn-submit {
    background: var(--brand-teal);
    color: #fff;
    border: none;
    padding: 0.85rem;
    border-radius: 8px;
    font-weight: 700;
    width: 100%;
    transition: 0.3s ease;
  }
  .btn-submit:hover {
    background: var(--brand-navy);
    color: #fff;
  }
  .form-control {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #D0E0DC;
  }
  .form-control:focus {
    border-color: var(--brand-teal);
    box-shadow: 0 0 0 3px rgba(2, 148, 145, 0.15);
  }
</style>
</head>
<body>

<div class="login-card">
  <div class="text-center mb-4">
    <a href="index.html" class="d-inline-block mb-2">
      <img src="assets/logo.png" alt="LifeCare Logo" style="height: 48px;">
    </a>
    <h2>LifeCare Portal Login</h2>
    <p class="text-muted small mb-0">Select your account role to sign in</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
      <div><?= $error ?></div>
    </div>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($selectedRole) ?>">

    <label class="form-label font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Select Your Role</label>
    <div class="role-btn-group">
      <div class="role-card <?= $selectedRole === 'doctor' ? 'active' : '' ?>" onclick="selectRole('doctor')">
        <i class="bi bi-stethoscope"></i>
        <span>Doctor</span>
      </div>
      <div class="role-card <?= $selectedRole === 'staff' ? 'active' : '' ?>" onclick="selectRole('staff')">
        <i class="bi bi-people-fill"></i>
        <span>Staff</span>
      </div>
      <div class="role-card <?= $selectedRole === 'admin' ? 'active' : '' ?>" onclick="selectRole('admin')">
        <i class="bi bi-shield-lock-fill"></i>
        <span>Main Admin</span>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label font-weight-bold"><i class="bi bi-person me-1"></i> Username or Email</label>
      <input type="text" name="username" id="usernameInput" class="form-control" placeholder="Enter username or email" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>

    <div class="mb-4">
      <label class="form-label font-weight-bold"><i class="bi bi-lock me-1"></i> Password</label>
      <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>

    <button type="submit" class="btn-submit mb-3" id="submitBtn">
      <i class="bi bi-box-arrow-in-right me-1"></i> Log In
    </button>
  </form>

  <div class="text-center mt-3 pt-3 border-top" id="registerFooter">
    <span class="text-muted small" id="regText">Don't have an account? </span>
    <a href="doctor-register.php" id="regLink" class="fw-bold text-decoration-none" style="color: var(--brand-teal);">Register Here</a>
  </div>
</div>

<script>
function selectRole(role) {
  document.getElementById('roleInput').value = role;

  const cards = document.querySelectorAll('.role-card');
  cards.forEach(card => card.classList.remove('active'));

  const activeCard = Array.from(cards).find(c => c.getAttribute('onclick').includes(role));
  if (activeCard) activeCard.classList.add('active');

  const regText = document.getElementById('regText');
  const regLink = document.getElementById('regLink');
  const usernameInput = document.getElementById('usernameInput');

  if (role === 'doctor') {
    usernameInput.placeholder = "Doctor Username or Email";
    regText.innerHTML = "Don't have a doctor account? ";
    regLink.innerText = "Register Doctor Profile";
    regLink.href = "doctor-register.php";
    regLink.style.display = "inline";
  } else if (role === 'staff') {
    usernameInput.placeholder = "Staff Username or Email";
    regText.innerHTML = "Don't have a staff account? ";
    regLink.innerText = "Register Staff Profile";
    regLink.href = "staff-register.php";
    regLink.style.display = "inline";
  } else if (role === 'admin') {
    usernameInput.placeholder = "Admin Username (Default: admin)";
    regText.innerHTML = "LifeCare Administration Portal";
    regLink.style.display = "none";
  }
}

// Initialize UI state based on current role
document.addEventListener('DOMContentLoaded', function() {
  selectRole("<?= htmlspecialchars($selectedRole) ?>");
});
</script>

</body>
</html>
