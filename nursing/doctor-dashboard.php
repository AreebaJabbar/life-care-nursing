<?php
require_once __DIR__ . '/config.php';
requireDoctorLogin();

$doctor = getLoggedInDoctor();

if (!$doctor) {
    // If session ID is somehow invalid, logout and redirect
    header('Location: doctor-logout.php');
    exit;
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $speciality       = trim($_POST['speciality'] ?? '');
    $qualifications   = trim($_POST['qualifications'] ?? '');
    $experience       = trim($_POST['experience'] ?? '');
    $hospitalName     = trim($_POST['hospitalName'] ?? '');
    $hospitalFee      = trim($_POST['hospitalFee'] ?? '');
    $hospitalSchedule = trim($_POST['hospitalSchedule'] ?? '');
    $videoFee         = trim($_POST['videoFee'] ?? '');
    $videoSchedule    = trim($_POST['videoSchedule'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $aboutBio         = trim($_POST['aboutBio'] ?? '');
    $whatsapp         = trim($_POST['whatsapp'] ?? '');

    if (empty($name) || empty($speciality)) {
        $error = 'Name and Speciality are required fields.';
    } else {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        $doctors = getData('doctors');
        $docId = (int)$_SESSION['doctor_id']; // Strictly locked to session ID

        foreach ($doctors as &$doc) {
            if ((int)$doc['id'] === $docId) {
                $doc['name']             = $name;
                $doc['speciality']       = $speciality;
                $doc['role']             = $speciality . ' Consultant';
                $doc['qualifications']   = $qualifications;
                $doc['experience']       = $experience;
                $doc['hospitalName']     = $hospitalName;
                $doc['hospitalFee']      = $hospitalFee;
                $doc['hospitalSchedule'] = $hospitalSchedule;
                $doc['videoFee']         = $videoFee;
                $doc['videoSchedule']    = $videoSchedule;
                $doc['description']      = $description;
                $doc['aboutBio']         = $aboutBio;
                $doc['whatsapp']         = $whatsapp;
                if ($imagePath) {
                    $doc['image']        = $imagePath;
                }
                break;
            }
        }

        saveData('doctors', $doctors);
        $doctor = getLoggedInDoctor(); // Refresh current profile data
        $msg = 'Profile updated successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard — <?= SITE_NAME ?></title>
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
    --bg-light: #F4F8F7;
  }
  body {
    font-family: 'Manrope', sans-serif;
    background: var(--bg-light);
    color: #2B3230;
  }
  .navbar-dash {
    background: var(--teal-900);
    color: #fff;
    padding: 0.9rem 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
  .navbar-dash .brand-title {
    font-family: 'Fraunces', serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
  }
  .dash-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(14,59,54,0.06);
    border: 1px solid #E2EDE9;
    padding: 2rem;
    margin-bottom: 2rem;
  }
  .dash-card h4 {
    font-family: 'Fraunces', serif;
    color: var(--teal-900);
    font-weight: 700;
    margin-bottom: 1.25rem;
    border-bottom: 2px solid #EAEFEF;
    padding-bottom: 0.75rem;
  }
  .profile-preview-img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid var(--brand-teal);
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
  }
  .btn-save {
    background: var(--brand-teal);
    color: #fff;
    border: none;
    padding: 0.85rem 2rem;
    border-radius: 8px;
    font-weight: 700;
    transition: 0.3s ease;
  }
  .btn-save:hover {
    background: var(--brand-navy);
    color: #fff;
  }
  .badge-approved {
    background: #D1E7DD;
    color: #0F5132;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.82rem;
  }
</style>
</head>
<body>

<!-- Header Nav -->
<nav class="navbar navbar-dash sticky-top">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-heart-pulse-fill text-warning fs-4"></i>
      <span class="brand-title"><?= SITE_NAME ?> — Doctor Dashboard</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="d-none d-md-inline text-light"><i class="bi bi-person-circle me-1"></i> Welcome, <?= htmlspecialchars($doctor['name']) ?></span>
      <a href="doctor-panel.php" target="_blank" class="btn btn-outline-light btn-sm"><i class="bi bi-globe me-1"></i> View Public Profile</a>
      <a href="doctor-logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <?php if (!empty($msg)): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2 fs-5"></i>
      <div><?= htmlspecialchars($msg) ?></div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
      <div><?= htmlspecialchars($error) ?></div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Profile Overview Sidebar -->
    <div class="col-lg-4 mb-4">
      <div class="dash-card text-center">
        <img src="<?= htmlspecialchars($doctor['image'] ?: 'assets/doctor_1.jpg') ?>" alt="Doctor Photo" class="profile-preview-img mb-3">
        <h4 class="mb-1 border-0 pb-0"><?= htmlspecialchars($doctor['name']) ?></h4>
        <p class="text-muted fw-bold mb-2"><?= htmlspecialchars($doctor['speciality'] ?: 'General Physician') ?></p>
        <span class="badge-approved"><i class="bi bi-patch-check-fill me-1"></i> Account Active</span>

        <hr class="my-3">

        <div class="text-start small">
          <p class="mb-1"><strong><i class="bi bi-journal-bookmark me-1 text-teal"></i> Qualifications:</strong> <?= htmlspecialchars($doctor['qualifications'] ?: 'MBBS') ?></p>
          <p class="mb-1"><strong><i class="bi bi-briefcase me-1 text-teal"></i> Experience:</strong> <?= htmlspecialchars($doctor['experience'] ?: '10 Years') ?></p>
          <p class="mb-1"><strong><i class="bi bi-whatsapp me-1 text-success"></i> WhatsApp:</strong> <?= htmlspecialchars($doctor['whatsapp'] ?: 'N/A') ?></p>
          <p class="mb-1"><strong><i class="bi bi-geo-alt me-1 text-primary"></i> Hospital:</strong> <?= htmlspecialchars($doctor['hospitalName'] ?: 'LifeCare Center') ?></p>
        </div>
      </div>
    </div>

    <!-- Edit Form Area -->
    <div class="col-lg-8">
      <div class="dash-card">
        <h4><i class="bi bi-pencil-square me-2" style="color: var(--brand-teal);"></i> Edit Doctor Profile</h4>

        <form method="POST" enctype="multipart/form-data">

          <!-- Basic Info -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Full Name *</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($doctor['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Speciality *</label>
              <input type="text" name="speciality" class="form-control" value="<?= htmlspecialchars($doctor['speciality'] ?? '') ?>" required placeholder="e.g. Cardiology Consultant">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Qualifications</label>
              <input type="text" name="qualifications" class="form-control" value="<?= htmlspecialchars($doctor['qualifications'] ?? '') ?>" placeholder="e.g. MBBS, FCPS (Cardiology)">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Experience</label>
              <input type="text" name="experience" class="form-control" value="<?= htmlspecialchars($doctor['experience'] ?? '') ?>" placeholder="e.g. 10+ Years">
            </div>
          </div>

          <!-- Contact & Photo -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">WhatsApp Number</label>
              <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($doctor['whatsapp'] ?? '') ?>" placeholder="923008053198">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Update Profile Photo</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
          </div>

          <!-- Hospital Consultation -->
          <h5 class="mt-4 mb-3 font-weight-bold text-dark"><i class="bi bi-hospital me-2" style="color: var(--brand-navy);"></i> In-Person / Hospital Consultation</h5>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label font-weight-bold">Clinic / Hospital Name</label>
              <input type="text" name="hospitalName" class="form-control" value="<?= htmlspecialchars($doctor['hospitalName'] ?? '') ?>" placeholder="LifeCare Center">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-bold">Hospital Fee (PKR)</label>
              <input type="text" name="hospitalFee" class="form-control" value="<?= htmlspecialchars($doctor['hospitalFee'] ?? '') ?>" placeholder="1500">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-bold">Hospital Schedule</label>
              <input type="text" name="hospitalSchedule" class="form-control" value="<?= htmlspecialchars($doctor['hospitalSchedule'] ?? '') ?>" placeholder="Mon - Sat: 02:00 PM - 05:00 PM">
            </div>
          </div>

          <!-- Video Consultation -->
          <h5 class="mt-4 mb-3 font-weight-bold text-dark"><i class="bi bi-camera-video me-2" style="color: var(--brand-navy);"></i> Video Consultation</h5>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Video Fee (PKR)</label>
              <input type="text" name="videoFee" class="form-control" value="<?= htmlspecialchars($doctor['videoFee'] ?? '') ?>" placeholder="1000">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Video Schedule</label>
              <input type="text" name="videoSchedule" class="form-control" value="<?= htmlspecialchars($doctor['videoSchedule'] ?? '') ?>" placeholder="Mon - Sun: 09:00 AM - 04:00 PM">
            </div>
          </div>

          <!-- Bio & Description -->
          <div class="mb-3">
            <label class="form-label font-weight-bold">Short Summary / Description</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of your services..."><?= htmlspecialchars($doctor['description'] ?? '') ?></textarea>
          </div>

          <div class="mb-4">
            <label class="form-label font-weight-bold">Detailed Bio / About Me</label>
            <textarea name="aboutBio" class="form-control" rows="4" placeholder="Detailed background, accomplishments, patient approach..."><?= htmlspecialchars($doctor['aboutBio'] ?? '') ?></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-save"><i class="bi bi-check2-circle me-1"></i> Save Profile Changes</button>
          </div>

        </form>

      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/bootstrap.bundle.min.js"></script>
</body>
</html>
