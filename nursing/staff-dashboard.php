<?php
require_once __DIR__ . '/config.php';
requireStaffLogin();

$staff = getLoggedInStaff();

if (!$staff) {
    header('Location: staff-logout.php');
    exit;
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $role        = trim($_POST['role'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $badge       = trim($_POST['badge'] ?? '');
    $shift       = trim($_POST['shift'] ?? '');
    $rate        = trim($_POST['rate'] ?? '');
    $skillsRaw   = trim($_POST['skills'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $whatsapp    = trim($_POST['whatsapp'] ?? '');

    $skillsArr = array_filter(array_map('trim', explode(',', $skillsRaw)));
    if (empty($skillsArr)) {
        $skillsArr = ['Patient Care', 'Vitals Tracking', 'Emergency Support'];
    }

    if (empty($name) || empty($role)) {
        $error = 'Name and Role are required fields.';
    } else {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        $staffList = getData('staff');
        $staffId = (int)$_SESSION['staff_id']; // Strictly locked to session ID

        foreach ($staffList as &$stf) {
            if ((int)$stf['id'] === $staffId) {
                $stf['name']        = $name;
                $stf['role']        = $role;
                $stf['city']        = $city;
                $stf['badge']       = $badge ?: $role;
                $stf['shift']       = $shift;
                $stf['rate']        = $rate;
                $stf['skills']      = array_values($skillsArr);
                $stf['description'] = $description;
                $stf['whatsapp']    = $whatsapp;
                if ($imagePath) {
                    $stf['image']   = $imagePath;
                }
                break;
            }
        }

        saveData('staff', $staffList);
        $staff = getLoggedInStaff(); // Refresh current data
        $msg = 'Staff Profile updated successfully!';
    }
}

// Convert skills array to string for edit input
$skillsInput = is_array($staff['skills'] ?? null) ? implode(', ', $staff['skills']) : ($staff['skills'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard — <?= SITE_NAME ?></title>
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
    background: var(--brand-navy);
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
    border: 4px solid var(--brand-navy);
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
  }
  .btn-save {
    background: var(--brand-navy);
    color: #fff;
    border: none;
    padding: 0.85rem 2rem;
    border-radius: 8px;
    font-weight: 700;
    transition: 0.3s ease;
  }
  .btn-save:hover {
    background: var(--brand-teal);
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
  .skill-badge {
    background: #E8F0EE;
    color: var(--teal-900);
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    display: inline-block;
    margin-right: 0.3rem;
    margin-bottom: 0.3rem;
  }
</style>
</head>
<body>

<!-- Header Nav -->
<nav class="navbar navbar-dash sticky-top">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-people-fill text-warning fs-4"></i>
      <span class="brand-title"><?= SITE_NAME ?> — Staff Dashboard</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="d-none d-md-inline text-light"><i class="bi bi-person-circle me-1"></i> Welcome, <?= htmlspecialchars($staff['name']) ?></span>
      <a href="staff-panel.php" target="_blank" class="btn btn-outline-light btn-sm"><i class="bi bi-globe me-1"></i> View Public Profile</a>
      <a href="staff-logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
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
    <!-- Sidebar Overview -->
    <div class="col-lg-4 mb-4">
      <div class="dash-card text-center">
        <img src="<?= htmlspecialchars($staff['image'] ?: 'assets/staff_1.jpg') ?>" alt="Staff Photo" class="profile-preview-img mb-3">
        <h4 class="mb-1 border-0 pb-0"><?= htmlspecialchars($staff['name']) ?></h4>
        <p class="text-muted fw-bold mb-2"><?= htmlspecialchars($staff['role'] ?: 'Registered Nurse') ?></p>
        <span class="badge-approved"><i class="bi bi-patch-check-fill me-1"></i> Verified Staff</span>

        <hr class="my-3">

        <div class="text-start small mb-3">
          <p class="mb-1"><strong><i class="bi bi-geo-alt me-1 text-primary"></i> City:</strong> <?= htmlspecialchars($staff['city'] ?: 'Faisalabad') ?></p>
          <p class="mb-1"><strong><i class="bi bi-clock-history me-1 text-teal"></i> Shift:</strong> <?= htmlspecialchars($staff['shift'] ?: '12-Hour Shift') ?></p>
          <p class="mb-1"><strong><i class="bi bi-currency-dollar me-1 text-success"></i> Rate:</strong> <?= htmlspecialchars($staff['rate'] ?: 'Rs. 2,200 / Day') ?></p>
          <p class="mb-1"><strong><i class="bi bi-whatsapp me-1 text-success"></i> WhatsApp:</strong> <?= htmlspecialchars($staff['whatsapp'] ?: 'N/A') ?></p>
        </div>

        <div class="text-start">
          <label class="form-label font-weight-bold small text-muted">Key Skills:</label>
          <div>
            <?php
            $skillsList = is_array($staff['skills'] ?? null) ? $staff['skills'] : explode(',', $staff['skills'] ?? '');
            foreach ($skillsList as $sk):
              if (trim($sk)):
            ?>
              <span class="skill-badge"><i class="bi bi-check2 me-1"></i><?= htmlspecialchars(trim($sk)) ?></span>
            <?php
              endif;
            endforeach;
            ?>
          </div>
        </div>

      </div>
    </div>

    <!-- Edit Form Area -->
    <div class="col-lg-8">
      <div class="dash-card">
        <h4><i class="bi bi-pencil-square me-2" style="color: var(--brand-navy);"></i> Edit Staff Profile</h4>

        <form method="POST" enctype="multipart/form-data">

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Full Name *</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($staff['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Professional Role *</label>
              <input type="text" name="role" class="form-control" value="<?= htmlspecialchars($staff['role'] ?? '') ?>" required placeholder="e.g. Senior Registered Nurse">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">City / Location</label>
              <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($staff['city'] ?? 'Faisalabad') ?>" placeholder="e.g. Faisalabad, Lahore">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Badge / Tagline</label>
              <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($staff['badge'] ?? '') ?>" placeholder="e.g. ICU Nurse / Senior Caregiver">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Shift Schedule</label>
              <input type="text" name="shift" class="form-control" value="<?= htmlspecialchars($staff['shift'] ?? '12-Hour Shift') ?>" placeholder="e.g. 12-Hour Shift, 24-Hour Live-in">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Service Rate</label>
              <input type="text" name="rate" class="form-control" value="<?= htmlspecialchars($staff['rate'] ?? 'Rs. 2,200 / Day') ?>" placeholder="e.g. Rs. 2,200 / Day">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label font-weight-bold">Skills (Comma-separated)</label>
            <input type="text" name="skills" class="form-control" value="<?= htmlspecialchars($skillsInput) ?>" placeholder="e.g. ICU Care, Vitals Tracking, IV Medication, Elderly Support">
            <div class="form-text">Separate skills with commas.</div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold">WhatsApp Number</label>
              <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($staff['whatsapp'] ?? '') ?>" placeholder="923008053198">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold">Update Profile Photo</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label font-weight-bold">Profile Description & Experience</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Detail your nursing/caregiving experience, specialty procedures, certifications..."><?= htmlspecialchars($staff['description'] ?? '') ?></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-save"><i class="bi bi-check2-circle me-1"></i> Save Staff Profile</button>
          </div>

        </form>

      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/bootstrap.bundle.min.js"></script>
</body>
</html>
