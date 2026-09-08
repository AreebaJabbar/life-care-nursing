<?php
require_once __DIR__ . '/config.php';
$staff = getData('staff');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Panel — <?= SITE_NAME ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
  :root{
    --teal-900:#0E3B36;
    --teal-700:#1B6B63;
    --teal-500:#2E8C82;
    --amber-500:#D9A441;
    --amber-100:#F7E8C9;
    --brand-navy:#03357A;
    --brand-teal:#029491;
  }
  *{box-sizing:border-box;}
  body{font-family:'Manrope',sans-serif; color:#2B3230; margin:0;}
  h1,h2,h3,h4,h5{font-family:'Fraunces',serif;}
  a{text-decoration:none;}

  .eyebrow{
    text-transform:uppercase; letter-spacing:.12em; font-weight:700; font-size:.78rem;
    display:inline-flex; align-items:center; gap:.6rem;
  }
  .eyebrow::before{content:""; width:26px; height:2px; background:var(--amber-500); display:inline-block;}

  .btn-care{
    background: var(--brand-teal); color:#fff; border:none; padding:1rem 1.9rem; border-radius:6px; font-weight:700; font-size:.85rem; letter-spacing:.03em; text-transform:uppercase; transition:.25s ease; display:inline-flex; align-items:center; gap:.5rem;
  }
  .btn-care:hover{background:var(--brand-navy); color:#fff;}

  .btn-outline-care{
    border:none; background:var(--brand-navy); color:#fff; padding:1rem 1.9rem; border-radius:6px; font-weight:700; font-size:.85rem; letter-spacing:.03em; text-transform:uppercase; display:inline-flex; align-items:center; gap:.5rem; transition:.25s ease;
  }
  .btn-outline-care:hover{background:var(--brand-teal); color:#fff;}

  /* Topbar & Nav */
  .topbar{background:var(--teal-900); color:#DCEAE7; font-size:.82rem; padding:.5rem 0;}
  .topbar a{color:#DCEAE7;}
  .topbar a:hover{color:var(--amber-500);}

  .navbar-care{background:#fff; padding:.9rem 0; box-shadow:0 2px 18px rgba(14,59,54,.08);}
  .brand-logo{height:52px;}
  .navbar-care .nav-link{color:var(--teal-900); font-weight:600; font-size:.92rem; margin:0 .7rem;}
  .navbar-care .nav-link:hover{color:var(--teal-700);}
  .navbar-care .nav-link.active{color:var(--brand-teal); font-weight:700;}

  /* Page Header */
  .page-header{
    position:relative; background:#E3EEEC; padding:0; color:var(--teal-900); overflow:hidden;
    min-height:340px; display:flex; align-items:center;
  }
  .page-header-photo{
    position:absolute; top:0; right:0; height:100%; width:58%;
    -webkit-mask-image:linear-gradient(to right, transparent 0%, #000 22%);
    mask-image:linear-gradient(to right, transparent 0%, #000 22%);
  }
  .page-header-photo img{width:100%; height:100%; object-fit:cover; object-position:center 20%;}
  .page-header-badge{
    position:absolute; top:50%; left:calc(42% - 34px); transform:translateY(-50%);
    width:68px; height:68px; border-radius:50%; background:#fff; z-index:3;
    display:flex; align-items:center; justify-content:center; box-shadow:0 12px 30px rgba(14,59,54,.18);
    color:var(--teal-700); font-size:1.5rem;
  }
  .page-header-inner{position:relative; z-index:2; padding:3.6rem 0;}
  .page-header h1{font-size:clamp(2rem,3.6vw,2.8rem); margin-bottom:.9rem; color:var(--teal-900);}
  .page-header .breadcrumb-care{
    color:var(--teal-700); font-size:.85rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
    display:flex; align-items:center; gap:.5rem;
  }
  .page-header .breadcrumb-care a{color:var(--teal-700);}

  /* Staff Cards */
  .panel-card{
    background:#fff; border-radius:14px; overflow:hidden;
    box-shadow:0 12px 35px rgba(14,59,54,.08); transition:.35s ease;
    height:100%; display:flex; flex-direction:column; border:1px solid #E8F0EE;
  }
  .panel-card:hover{
    transform:translateY(-7px); box-shadow:0 22px 48px rgba(14,59,54,.16); border-color:var(--brand-teal);
  }
  .panel-card-img-holder{
    position:relative; width:100%; height:260px; overflow:hidden; background:#EAF3F1;
  }
  .panel-card-img-holder img{
    width:100%; height:100%; object-fit:cover; display:block; transition:transform .4s ease;
  }
  .panel-card:hover .panel-card-img-holder img{transform:scale(1.06);}
  .panel-card-badge{
    position:absolute; bottom:12px; left:12px; background:var(--brand-navy); color:#fff;
    font-size:.75rem; font-weight:700; padding:.35rem .85rem; border-radius:20px;
    letter-spacing:.03em; text-transform:uppercase; box-shadow:0 4px 12px rgba(0,0,0,.2);
  }
  .panel-card-body{
    padding:1.6rem; flex:1; display:flex; flex-direction:column; justify-content:space-between;
  }
  .panel-card-name{
    font-family:var(--font-display); font-size:1.25rem; font-weight:700; color:var(--teal-900); margin-bottom:.3rem;
  }
  .panel-card-role{
    color:var(--brand-teal); font-weight:700; font-size:.85rem; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.8rem;
  }
  .panel-card-desc{
    font-size:.92rem; color:#52605C; line-height:1.65; margin-bottom:1.2rem;
  }
  .panel-card-btn{
    width:100%; justify-content:center; padding:.75rem 1rem; font-size:.82rem; border-radius:6px;
  }

  /* Footer & Contact Floating Stack */
  footer{background:#E3EEEC; color:#3D4B48; padding:4rem 0 0;}
  footer h5{color:var(--teal-900); font-size:1rem; margin-bottom:1.1rem; font-family:'Fraunces',serif;}
  footer p{font-size:.9rem; line-height:1.7; color:#5C6A66;}
  footer ul{list-style:none; padding:0; margin:0;}
  footer ul li{margin-bottom:.6rem; font-size:.9rem; display:flex; align-items:center; gap:.5rem;}
  footer ul li a{color:#3D4B48;}
  .footer-logo{height:50px; margin-bottom:1rem;}
  .footer-bottom{
    border-top:1px solid rgba(14,59,54,.1); margin-top:2.5rem; padding:1.3rem 0;
    display:flex; justify-content:space-between; flex-wrap:wrap; gap:.7rem; font-size:.82rem; color:#5C6A66;
  }
  .floating-contact-stack{position:fixed; right:22px; bottom:22px; z-index:999; display:flex; flex-direction:column; align-items:flex-end; gap:.7rem;}
  .floating-contact-btn{display:inline-flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 8px 20px rgba(0,0,0,.25); transition:.25s ease; text-decoration:none;}
  .btn-call{width:50px; height:50px; border-radius:50%; font-size:1.3rem; background:var(--brand-navy);}
  .btn-wa{background:#25D366; color:#fff; padding:.7rem 1.25rem; border-radius:50px; font-weight:700; font-size:.9rem; gap:.5rem; box-shadow:0 8px 22px rgba(37,211,102,.45);}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div class="container d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex gap-3">
      <a href="tel:+923008053198"><i class="bi bi-telephone-fill me-1"></i> 0300-8053198</a>
      <a href="mailto:<?= CONTACT_EMAIL ?>" class="d-none d-sm-inline"><i class="bi bi-envelope-fill me-1"></i> <?= CONTACT_EMAIL ?></a>
    </div>
    <div class="d-flex gap-3">
      <a href="#"><i class="bi bi-facebook"></i></a>
      <a href="#"><i class="bi bi-instagram"></i></a>
      <a href="#"><i class="bi bi-youtube"></i></a>
      <a href="#"><i class="bi bi-tiktok"></i></a>
    </div>
  </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-care sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.html">
      <img src="assets/logo.png" alt="LifeCare Nursing & Medical Services" class="brand-logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="index.html#home">Home</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Our Services</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="doctor-consultation.html">Doctor Consultation</a></li>
            <li><a class="dropdown-item" href="home-nursing-care.html">Home Nursing Care</a></li>
            <li><a class="dropdown-item" href="diagnostic-services.html">Diagnostic Services</a></li>
            <li><a class="dropdown-item" href="elderly-senior-care.html">Elderly / Senior Care</a></li>
            <li><a class="dropdown-item" href="physiotherapy.html">Physiotherapy</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="index.html#blog">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="index.html#about">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="doctor-panel.php">Doctor Panel</a></li>
        <li class="nav-item"><a class="nav-link active" href="staff-panel.php">Staff Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="team.php">Our Team</a></li>
        <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#contactModal">Contact Us</a></li>
        <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a href="https://wa.me/923008053198?text=Hello%2C%20I%20would%20like%20to%20request%20nursing%20staff" target="_blank" class="btn-care">Get Nursing Care</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- PAGE HEADER -->
<section class="page-header">
  <div class="page-header-photo">
    <img src="assets/staff_1.jpg" alt="Staff Panel">
  </div>
  <div class="page-header-badge"><i class="bi bi-people-fill"></i></div>
  <div class="container page-header-inner">
    <div class="eyebrow mb-3">Healthcare & Nursing Team</div>
    <h1>Staff Panel</h1>
    <div class="breadcrumb-care"><a href="index.html">Home</a> <i class="bi bi-chevron-right"></i> Staff Panel</div>
  </div>
</section>

<!-- MAIN STAFF PANEL GRID -->
<section class="py-5" style="background:#FAF8F3;">
  <div class="container py-4">
    <div class="text-center mb-5">
      <h2 class="display-6 font-weight-bold" style="color: var(--teal-900);">Our Healthcare & Nursing Staff</h2>
      <p class="text-muted mx-auto" style="max-width: 650px; font-size: 1rem;">
        Certified, compassionate male and female nurses, caregivers, and patient attendants providing dedicated 24/7 care.
      </p>
    </div>

    <div class="row g-4">
      <?php if (empty($staff)): ?>
        <div class="col-12 text-center py-5 text-muted">No staff cards uploaded yet. Check back soon or upload from Admin Panel.</div>
      <?php else: ?>
        <?php foreach ($staff as $stf): ?>
          <?php 
            $waNumber = !empty($stf['whatsapp']) ? preg_replace('/[^0-9]/', '', $stf['whatsapp']) : '923008053198';
            $waMsg = urlencode("Hello, I would like to request nursing staff member " . $stf['name'] . " (" . $stf['role'] . " - " . $stf['badge'] . "). Please share details.");
            $waUrl = "https://wa.me/{$waNumber}?text={$waMsg}";
          ?>
          <div class="col-md-6 col-lg-3">
            <div class="panel-card">
              <div class="panel-card-img-holder">
                <img src="<?= htmlspecialchars($stf['image']) ?>" alt="<?= htmlspecialchars($stf['name']) ?>">
                <span class="panel-card-badge"><?= htmlspecialchars($stf['badge']) ?></span>
              </div>
              <div class="panel-card-body">
                <div>
                  <div class="panel-card-name"><?= htmlspecialchars($stf['name']) ?></div>
                  <div class="panel-card-role"><?= htmlspecialchars($stf['role']) ?></div>
                  <p class="panel-card-desc"><?= htmlspecialchars($stf['description']) ?></p>
                </div>
                <a href="<?= $waUrl ?>" target="_blank" class="btn-outline-care panel-card-btn">
                  <i class="bi bi-person-check me-1"></i> Request Staff
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <img src="assets/logo.png" alt="LifeCare Nursing & Medical Services" class="footer-logo">
        <p>LifeCare provides nursing, elderly care, physiotherapy, diagnostic services and other healthcare support for patients at home.</p>
      </div>
      <div class="col-lg-2 col-6">
        <h5>Quick Links</h5>
        <ul>
          <li><a href="index.html#home"><i class="bi bi-chevron-right"></i> Home</a></li>
          <li><a href="doctor-panel.php"><i class="bi bi-chevron-right"></i> Doctor Panel</a></li>
          <li><a href="staff-panel.php"><i class="bi bi-chevron-right"></i> Staff Panel</a></li>
          <li><a href="team.php"><i class="bi bi-chevron-right"></i> Our Team</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-6">
        <h5>Our Services</h5>
        <ul>
          <li><a href="home-nursing-care.html"><i class="bi bi-chevron-right"></i> Home Nursing Care</a></li>
          <li><a href="elderly-senior-care.html"><i class="bi bi-chevron-right"></i> Elderly / Senior Care</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h5>Get in Touch</h5>
        <ul>
          <li><a href="tel:+923008053198"><i class="bi bi-telephone-fill me-2"></i>0300-8053198</a></li>
          <li><a href="mailto:<?= CONTACT_EMAIL ?>"><i class="bi bi-envelope-fill me-2"></i><?= CONTACT_EMAIL ?></a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div>Copyright &copy; <?= date('Y') ?> LifeCare Nursing & Medical Services. All Rights Reserved.</div>
    </div>
  </div>
</footer>

<!-- FLOATING CONTACT STACK -->
<div class="floating-contact-stack">
  <a href="tel:+923008053198" class="floating-contact-btn btn-call" title="Call Us Now">
    <i class="bi bi-telephone-fill"></i>
  </a>
  <a href="https://wa.me/923008053198?text=Hello%2C%20I%20would%20like%20to%20request%20nursing%20staff" target="_blank" class="floating-contact-btn btn-wa">
    <i class="bi bi-whatsapp"></i> <span>Get Nursing Staff</span>
  </a>
</div>

<!-- CONTACT US MODAL -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header text-white" style="background: var(--brand-navy); padding: 1.25rem 1.75rem;">
        <h5 class="modal-title d-flex align-items-center gap-2" style="font-weight: 700;">
          <i class="bi bi-envelope-paper-fill text-warning"></i> Contact Us — Send a Message
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 p-md-5" style="background: #FAF8F3;">
        <p class="text-muted mb-4">Tell us what you need help with. Messages are sent to <strong>lifecarenursing5@gmail.com</strong>.</p>
        
        <div id="contactFormAlert" class="alert alert-success d-none mb-4" role="alert"></div>

        <form id="contactForm" onsubmit="handleContactSubmit(event)">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label font-weight-bold" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Name</label>
              <input type="text" class="form-control form-control-lg" id="contactName" placeholder="e.g. Ali Khan" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Phone / WhatsApp</label>
              <input type="tel" class="form-control form-control-lg" id="contactPhone" placeholder="0300-8053198" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Email</label>
              <input type="email" class="form-control form-control-lg" id="contactEmail" placeholder="name@example.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-bold" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Select Service</label>
              <select class="form-select form-select-lg" id="contactService">
                <option value="Home Nursing Care" selected>Home Nursing Care</option>
                <option value="Elderly / Senior Care">Elderly / Senior Care</option>
                <option value="Patient Care Attendant">Patient Care Attendant</option>
                <option value="Physiotherapy">Physiotherapy</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label font-weight-bold" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Your Message *</label>
              <textarea class="form-control" id="contactMessage" rows="4" placeholder="Please describe how we can assist you..." required></textarea>
            </div>
            <div class="col-12 mt-4 d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn-care px-4 py-2" id="submitBtn">
                <i class="bi bi-send-fill me-1"></i> Send Request
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function handleContactSubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('submitBtn');
  const alertBox = document.getElementById('contactFormAlert');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending...';

  const formData = new FormData();
  formData.append('name', document.getElementById('contactName').value);
  formData.append('phone', document.getElementById('contactPhone').value);
  formData.append('email', document.getElementById('contactEmail').value);
  formData.append('service', document.getElementById('contactService').value);
  formData.append('message', document.getElementById('contactMessage').value);

  fetch('send_email.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    alertBox.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger') + ' mb-4';
    alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> ' + data.message;
    alertBox.classList.remove('d-none');
    if (data.success) {
      document.getElementById('contactForm').reset();
    }
  })
  .catch(err => {
    const name = document.getElementById('contactName').value;
    const phone = document.getElementById('contactPhone').value;
    const email = document.getElementById('contactEmail').value;
    const service = document.getElementById('contactService').value;
    const message = document.getElementById('contactMessage').value;
    window.location.href = `mailto:lifecarenursing5@gmail.com?subject=${encodeURIComponent(name + ' - ' + service)}&body=${encodeURIComponent(message + '\nPhone: ' + phone)}`;
  })
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Send Request';
  });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
