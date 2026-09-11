<?php
require_once __DIR__ . '/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $speciality = trim($_POST['speciality'] ?? '');

    if (empty($name) || empty($username) || empty($password) || empty($speciality)) {
        $error = 'All fields (Name, Username/Email, Password, Speciality) are required.';
    } elseif (getDoctorByUsername($username)) {
        $error = 'This Username / Email is already registered. Please choose another or login.';
    } else {
        $doctors = getData('doctors');
        $newId = !empty($doctors) ? max(array_column($doctors, 'id')) + 1 : 1;

        $newDoctor = [
            'id'               => $newId,
            'name'             => $name,
            'username'         => $username,
            'password'         => password_hash($password, PASSWORD_DEFAULT),
            'status'           => 'pending',
            'speciality'       => $speciality,
            'role'             => $speciality . ' Consultant',
            'qualifications'   => 'MBBS, FCPS',
            'badge'            => 'PMC Verified',
            'experience'       => '5+ Years',
            'waitTime'         => 'Under 15 Mins',
            'satisfaction'     => '98%',
            'hospitalName'     => 'LifeCare Clinical Center',
            'hospitalFee'      => '1500',
            'hospitalSchedule' => 'Mon - Sat: 02:00 PM - 05:00 PM',
            'videoFee'         => '1000',
            'videoSchedule'    => 'Mon - Sun: 09:00 AM - 04:00 PM',
            'image'            => 'assets/doctor_1.jpg',
            'description'      => 'Specialist doctor providing home consultations and clinical medical services.',
            'aboutBio'         => 'Experienced in treating general and specialized clinical cases with dedicated patient care.',
            'whatsapp'         => '923008053198'
        ];

        $doctors[] = $newDoctor;
        saveData('doctors', $doctors);

        $success = 'Account created successfully! Your registration is currently <strong>Pending Admin Approval</strong>. You can log in once an admin approves your profile.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Self-Registration — <?= SITE_NAME ?></title>
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
    --teal-500: #2E8C82;
    --amber-500: #D9A441;
    --bg-soft: #F4F8F7;
    --font-display: 'Fraunces', serif;
  }
  body {
    font-family: 'Manrope', sans-serif;
    background: var(--bg-soft);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    margin: 0;
  }
  .auth-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
  }
  .auth-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(3, 53, 122, 0.08);
    width: 100%;
    max-width: 520px;
    padding: 2.5rem;
    border: 1px solid #E2EDE9;
  }
  .auth-card h2 {
    font-family: 'Fraunces', serif;
    color: var(--teal-900);
    font-weight: 700;
  }
  .auth-badge {
    background: rgba(2, 148, 145, 0.1);
    color: var(--brand-teal);
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 1rem;
  }
  .btn-care {
    background: var(--brand-teal);
    color: #fff;
    border: none;
    padding: 0.85rem;
    border-radius: 8px;
    font-weight: 700;
    width: 100%;
    transition: 0.3s ease;
  }
  .btn-care:hover {
    background: var(--brand-navy);
    color: #fff;
  }
  .form-control, .form-select {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #D0E0DC;
  }
  .form-control:focus, .form-select:focus {
    border-color: var(--brand-teal);
    box-shadow: 0 0 0 3px rgba(2, 148, 145, 0.15);
  }

  /* ---------- Footer (same as homepage) ---------- */
  footer{
    background:var(--teal-900);
    color:#BFD6D2;
    padding:4rem 0 1.5rem;
    font-size:.9rem;
  }
  footer h5{color:#fff; font-size:1rem; margin-bottom:1.1rem;}
  footer a{color:#BFD6D2; text-decoration:none;}
  footer a:hover{color:var(--amber-500);}
  footer ul{list-style:none; padding:0; margin:0;}
  footer li{margin-bottom:.6rem;}
  .footer-brand{font-family:var(--font-display); color:#fff; font-size:1.3rem; font-weight:700;}
  .footer-logo{height:72px; width:auto; background:#fff; border-radius:8px; padding:.35rem .5rem;}
  .footer-bottom{
    border-top:1px solid rgba(255,255,255,.12);
    margin-top:2.5rem; padding-top:1.5rem;
    font-size:.8rem; color:#9FC2BC;
    display:flex; justify-content:space-between; flex-wrap:wrap; gap:.5rem;
  }
  .social-dot{
    width:34px; height:34px; border-radius:50%; background:var(--teal-500); color:#fff;
    display:inline-flex; align-items:center; justify-content:center; margin-right:.5rem;
  }
  .social-dot i{color:#fff;}
  .social-dot:hover{background:var(--brand-navy); color:#fff;}
  .social-dot:hover i{color:#fff;}
</style>
</head>
<body>

<div class="auth-wrapper">
<div class="auth-card">
  <div class="text-center mb-4">
    <span class="auth-badge"><i class="bi bi-person-plus-fill me-1"></i> Doctor Portal</span>
    <h2>Doctor Self-Registration</h2>
    <p class="text-muted small">Create your profile to offer medical consultations on LifeCare</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      <?= $success ?>
    </div>
    <div class="text-center mt-3">
      <a href="doctor-login.php" class="btn btn-care">Proceed to Doctor Login</a>
    </div>
  <?php else: ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label font-weight-bold"><i class="bi bi-person me-1"></i> Full Name</label>
        <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Haris Abbasi" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label font-weight-bold"><i class="bi bi-envelope me-1"></i> Username or Email</label>
        <input type="text" name="username" class="form-control" placeholder="e.g. drharis@lifecare.com" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label font-weight-bold"><i class="bi bi-lock me-1"></i> Password</label>
        <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
      </div>

      <div class="mb-4">
        <label class="form-label font-weight-bold"><i class="bi bi-award me-1"></i> Speciality</label>
        <select name="speciality" class="form-select" required>
          <option value="">-- Select Speciality --</option>
          <option value="Gynecologist" <?= (($_POST['speciality'] ?? '') === 'Gynecologist') ? 'selected' : '' ?>>Gynecologist</option>
          <option value="Dentist" <?= (($_POST['speciality'] ?? '') === 'Dentist') ? 'selected' : '' ?>>Dentist</option>
          <option value="Dermatologist" <?= (($_POST['speciality'] ?? '') === 'Dermatologist') ? 'selected' : '' ?>>Dermatologist</option>
          <option value="Cardiologist" <?= (($_POST['speciality'] ?? '') === 'Cardiologist') ? 'selected' : '' ?>>Cardiologist</option>
          <option value="Neurologist" <?= (($_POST['speciality'] ?? '') === 'Neurologist') ? 'selected' : '' ?>>Neurologist</option>
          <option value="ENT Specialist" <?= (($_POST['speciality'] ?? '') === 'ENT Specialist') ? 'selected' : '' ?>>ENT Specialist</option>
          <option value="Pediatrician" <?= (($_POST['speciality'] ?? '') === 'Pediatrician') ? 'selected' : '' ?>>Pediatrician</option>
          <option value="Gastroenterologist" <?= (($_POST['speciality'] ?? '') === 'Gastroenterologist') ? 'selected' : '' ?>>Gastroenterologist</option>
          <option value="General Physician" <?= (($_POST['speciality'] ?? '') === 'General Physician') ? 'selected' : '' ?>>General Physician</option>
          <option value="Plastic Surgeon" <?= (($_POST['speciality'] ?? '') === 'Plastic Surgeon') ? 'selected' : '' ?>>Plastic Surgeon</option>
          <option value="Urologist" <?= (($_POST['speciality'] ?? '') === 'Urologist') ? 'selected' : '' ?>>Urologist</option>
          <option value="Psychiatrist" <?= (($_POST['speciality'] ?? '') === 'Psychiatrist') ? 'selected' : '' ?>>Psychiatrist</option>
        </select>
      </div>

      <button type="submit" class="btn btn-care mb-3">Register Profile</button>
    </form>

    <div class="text-center mt-3 pt-3 border-top">
      <span class="text-muted small">Already registered? </span>
      <a href="doctor-login.php" class="fw-bold text-decoration-none" style="color: var(--brand-navy);">Login Here</a>
    </div>

  <?php endif; ?>
</div>
</div>

<!-- ================= FOOTER ================= -->
<footer id="contact">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <img src="assets/logo.png" alt="LifeCare Nursing & Medical Services" class="footer-logo mb-3">
        <p>LifeCare provides nursing, elderly care, physiotherapy, diagnostic and other healthcare services for patients who need support at home.</p>
        <div class="mt-3">
          <a href="#" class="social-dot"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-dot"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-dot"><i class="bi bi-youtube"></i></a>
          <a href="#" class="social-dot"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-6">
        <h5>Quick Links</h5>
        <ul>
          <li><a href="index.html#home">Home</a></li>
          <li><a href="index.html#services">Our Services</a></li>
          <li><a href="index.html#about">About Us</a></li>
          <li><a href="index.html#contact">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-6">
        <h5>Our Services</h5>
        <ul>
          <li><a href="doctor-consultation.html">Doctor Consultation</a></li>
          <li><a href="home-nursing-care.html">Home Nursing Care</a></li>
          <li><a href="elderly-senior-care.html">Elderly / Senior Care</a></li>
          <li><a href="physiotherapy.html">Physiotherapy</a></li>
          <li><a href="diagnostic-services.html">Diagnostic Services</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h5>Get in Touch</h5>
        <ul>
          <li><a href="tel:+923008053198"><i class="bi bi-telephone-fill me-2"></i>0300-8053198</a></li>
          <li><a href="mailto:lifecarenursing5@gmail.com"><i class="bi bi-envelope-fill me-2"></i>lifecarenursing5@gmail.com</a></li>
          <li><i class="bi bi-geo-alt-fill me-2"></i>Near Zee Garden Main Shekhupura / Lahore Road Faislabad</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div>Copyright &copy; 2026 LifeCare Nursing & Medical Services. All Rights Reserved.</div>
      <div><a href="#">Terms & Service</a> &nbsp;|&nbsp; <a href="#">Privacy Policy</a></div>
    </div>
  </div>
</footer>

</body>
</html>
