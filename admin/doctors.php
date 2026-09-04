<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$type = 'doctors';
$doctors = getData($type);
$msg = '';
$error = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
    $doctors = array_filter($doctors, function($d) use ($deleteId) {
        return (int)$d['id'] !== $deleteId;
    });
    $doctors = array_values($doctors);
    saveData($type, $doctors);
    header('Location: doctors.php?msg=deleted');
    exit;
}

// Handle Add / Edit POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id               = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name             = trim($_POST['name'] ?? '');
    $speciality       = trim($_POST['speciality'] ?? 'General Physician');
    $role             = trim($_POST['role'] ?? '');
    $qualifications   = trim($_POST['qualifications'] ?? '');
    $badge            = trim($_POST['badge'] ?? 'PMC Verified');
    $experience       = trim($_POST['experience'] ?? '10 Years');
    $waitTime         = trim($_POST['waitTime'] ?? 'Under 15 Mins');
    $satisfaction     = trim($_POST['satisfaction'] ?? '97%');
    $hospitalName     = trim($_POST['hospitalName'] ?? '');
    $hospitalFee      = trim($_POST['hospitalFee'] ?? '1500');
    $hospitalSchedule = trim($_POST['hospitalSchedule'] ?? '');
    $videoFee         = trim($_POST['videoFee'] ?? '1000');
    $videoSchedule    = trim($_POST['videoSchedule'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $aboutBio         = trim($_POST['aboutBio'] ?? '');
    $whatsapp         = trim($_POST['whatsapp'] ?? '923008053198');

    if (empty($name) || empty($speciality)) {
        $error = 'Doctor Name and Speciality are required.';
    } else {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        if ($id > 0) {
            foreach ($doctors as &$doc) {
                if ((int)$doc['id'] === $id) {
                    $doc['name']             = $name;
                    $doc['speciality']       = $speciality;
                    $doc['role']             = $role ?: $speciality;
                    $doc['qualifications']   = $qualifications;
                    $doc['badge']            = $badge;
                    $doc['experience']       = $experience;
                    $doc['waitTime']         = $waitTime;
                    $doc['satisfaction']     = $satisfaction;
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
            saveData($type, $doctors);
            header('Location: doctors.php?msg=updated');
            exit;
        } else {
            $newId = !empty($doctors) ? max(array_column($doctors, 'id')) + 1 : 1;
            $newDoctor = [
                'id'               => $newId,
                'name'             => $name,
                'speciality'       => $speciality,
                'role'             => $role ?: $speciality,
                'qualifications'   => $qualifications ?: 'MBBS, FCPS',
                'badge'            => $badge ?: 'PMC Verified',
                'pmcVerified'      => true,
                'waitTime'         => $waitTime,
                'experience'       => $experience,
                'satisfaction'     => $satisfaction,
                'hospitalName'     => $hospitalName ?: 'LifeCare Clinical Center',
                'hospitalFee'      => $hospitalFee,
                'hospitalSchedule' => $hospitalSchedule ?: 'Mon - Sat: 02:00 PM - 05:00 PM',
                'videoFee'         => $videoFee,
                'videoSchedule'    => $videoSchedule ?: 'Mon - Sun: 09:00 AM - 04:00 PM',
                'image'            => $imagePath ?: 'assets/doctor_1.jpg',
                'description'      => $description,
                'aboutBio'         => $aboutBio,
                'whatsapp'         => $whatsapp
            ];
            $doctors[] = $newDoctor;
            saveData($type, $doctors);
            header('Location: doctors.php?msg=added');
            exit;
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $msg = 'New Doctor Profile created successfully!';
    if ($_GET['msg'] === 'updated') $msg = 'Doctor Profile updated successfully!';
    if ($_GET['msg'] === 'deleted') $msg = 'Doctor Profile removed successfully!';
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 text-dark">Doctor Panel Directory Management</h2>
        <p class="text-muted m-0">Manage doctor profiles, specialities, qualifications, fees, schedules, and profile bios.</p>
    </div>
    <button type="button" class="btn-care" data-bs-toggle="modal" data-bs-target="#doctorModal" onclick="resetDoctorForm()">
        <i class="bi bi-person-plus-fill me-1"></i> Add New Doctor Profile
    </button>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card-custom">
    <div class="card-custom-header">
        <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-person-badge-fill text-primary me-2"></i> Registered Doctors (<?= count($doctors) ?>)</h5>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th>Doctor Name & Qualifications</th>
                    <th>Speciality</th>
                    <th>Hospital & Fees</th>
                    <th>Experience</th>
                    <th class="text-end" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($doctors)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No doctor profiles found. Click "Add New Doctor Profile" to create one.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($doctors as $doc): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($doc['image']) ?>" alt="Photo" class="thumb-preview" onerror="this.src='../assets/doctor_1.jpg'">
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($doc['name']) ?></div>
                                <div class="text-muted" style="font-size: 0.82rem;"><?= htmlspecialchars($doc['qualifications'] ?? $doc['badge'] ?? '') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><?= htmlspecialchars($doc['speciality'] ?? $doc['role'] ?? 'General') ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary" style="font-size: 0.85rem;"><?= htmlspecialchars($doc['hospitalName'] ?? 'LifeCare Clinic') ?></div>
                                <div class="text-success small fw-bold">Fee: Rs. <?= htmlspecialchars($doc['hospitalFee'] ?? '1500') ?></div>
                            </td>
                            <td>
                                <span class="text-muted small"><?= htmlspecialchars($doc['experience'] ?? '10 Years') ?></span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editDoctor(<?= htmlspecialchars(json_encode($doc)) ?>)" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="doctors.php?action=delete&id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this doctor?');" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD / EDIT DOCTOR MODAL -->
<div class="modal fade" id="doctorModal" tabindex="-1" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white p-4" style="background: var(--teal-900);">
                <h4 class="modal-title font-weight-bold m-0" id="doctorModalTitle">
                    <i class="bi bi-person-plus-fill me-2" style="color: var(--amber-500);"></i> Add New Doctor Profile
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #FAF8F3;">
                <form method="POST" enctype="multipart/form-data" id="doctorForm">
                    <input type="hidden" name="id" id="docId" value="0">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="docName" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Doctor Full Name *</label>
                            <input type="text" class="form-control" name="name" id="docName" placeholder="e.g. Dr. Abeera Ali" required>
                        </div>
                        <div class="col-md-6">
                            <label for="docSpeciality" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Speciality Category *</label>
                            <select class="form-select" name="speciality" id="docSpeciality">
                                <option value="Gynecologist">Gynecologist</option>
                                <option value="Cardiologist">Cardiologist</option>
                                <option value="Pediatrician">Pediatrician</option>
                                <option value="Neurologist">Neurologist</option>
                                <option value="Dermatologist">Dermatologist</option>
                                <option value="General Physician">General Physician</option>
                                <option value="Physiotherapist">Physiotherapist</option>
                                <option value="Urologist">Urologist</option>
                                <option value="Psychiatrist">Psychiatrist</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="docRole" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Designation / Title</label>
                            <input type="text" class="form-control" name="role" id="docRole" placeholder="e.g. Gynecologist & Laparoscopic Surgeon">
                        </div>
                        <div class="col-md-6">
                            <label for="docQualifications" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Degrees & Qualifications</label>
                            <input type="text" class="form-control" name="qualifications" id="docQualifications" placeholder="e.g. MBBS, FCPS (Gyn & Obs)">
                        </div>

                        <div class="col-md-4">
                            <label for="docExperience" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Experience</label>
                            <input type="text" class="form-control" name="experience" id="docExperience" placeholder="10 Years" value="10 Years">
                        </div>
                        <div class="col-md-4">
                            <label for="docWaitTime" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Wait Time</label>
                            <input type="text" class="form-control" name="waitTime" id="docWaitTime" placeholder="Under 15 Mins" value="Under 15 Mins">
                        </div>
                        <div class="col-md-4">
                            <label for="docSatisfaction" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Satisfaction Rate</label>
                            <input type="text" class="form-control" name="satisfaction" id="docSatisfaction" placeholder="97%" value="97%">
                        </div>

                        <div class="col-md-6">
                            <label for="docHospitalName" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Hospital / Clinic Name & Address</label>
                            <input type="text" class="form-control" name="hospitalName" id="docHospitalName" placeholder="e.g. Anmol Hospital (Jhang Road, Faisalabad)">
                        </div>
                        <div class="col-md-3">
                            <label for="docHospitalFee" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Hospital Fee (Rs.)</label>
                            <input type="text" class="form-control" name="hospitalFee" id="docHospitalFee" placeholder="1500" value="1500">
                        </div>
                        <div class="col-md-3">
                            <label for="docVideoFee" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Video Fee (Rs.)</label>
                            <input type="text" class="form-control" name="videoFee" id="docVideoFee" placeholder="1000" value="1000">
                        </div>

                        <div class="col-md-6">
                            <label for="docHospitalSchedule" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Hospital Timings Schedule</label>
                            <input type="text" class="form-control" name="hospitalSchedule" id="docHospitalSchedule" placeholder="Mon - Sat: 02:00 PM - 03:30 PM">
                        </div>
                        <div class="col-md-6">
                            <label for="docVideoSchedule" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Video / Home Timings Schedule</label>
                            <input type="text" class="form-control" name="videoSchedule" id="docVideoSchedule" placeholder="Mon - Sun: 09:00 AM - 04:00 PM">
                        </div>

                        <div class="col-md-6">
                            <label for="docImage" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Doctor Photo</label>
                            <input type="file" class="form-control" name="image" id="docImage" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label for="docWhatsapp" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">WhatsApp Number</label>
                            <input type="text" class="form-control" name="whatsapp" id="docWhatsapp" placeholder="923008053198" value="923008053198">
                        </div>

                        <div class="col-12">
                            <label for="docDescription" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Short Description Excerpt</label>
                            <textarea class="form-control" name="description" id="docDescription" rows="2" placeholder="Short 1-2 sentence overview of experience and services..."></textarea>
                        </div>

                        <div class="col-12">
                            <label for="docAboutBio" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Full Profile Biography & Services (HTML/Text)</label>
                            <textarea class="form-control" name="aboutBio" id="docAboutBio" rows="4" placeholder="<p>Full doctor biography, medical background, services offered...</p>"></textarea>
                        </div>

                        <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-care px-4" style="border: none; cursor: pointer;">
                                <i class="bi bi-check-circle-fill me-1"></i> Save Doctor Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetDoctorForm() {
    document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-person-plus-fill me-2" style="color: var(--amber-500);"></i> Add New Doctor Profile';
    document.getElementById('docId').value = 0;
    document.getElementById('doctorForm').reset();
}

function editDoctor(doc) {
    document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-pencil-square me-2" style="color: var(--amber-500);"></i> Edit Doctor Profile';
    document.getElementById('docId').value = doc.id;
    document.getElementById('docName').value = doc.name || '';
    document.getElementById('docSpeciality').value = doc.speciality || 'General Physician';
    document.getElementById('docRole').value = doc.role || '';
    document.getElementById('docQualifications').value = doc.qualifications || '';
    document.getElementById('docExperience').value = doc.experience || '10 Years';
    document.getElementById('docWaitTime').value = doc.waitTime || 'Under 15 Mins';
    document.getElementById('docSatisfaction').value = doc.satisfaction || '97%';
    document.getElementById('docHospitalName').value = doc.hospitalName || '';
    document.getElementById('docHospitalFee').value = doc.hospitalFee || '1500';
    document.getElementById('docVideoFee').value = doc.videoFee || '1000';
    document.getElementById('docHospitalSchedule').value = doc.hospitalSchedule || '';
    document.getElementById('docVideoSchedule').value = doc.videoSchedule || '';
    document.getElementById('docWhatsapp').value = doc.whatsapp || '923008053198';
    document.getElementById('docDescription').value = doc.description || '';
    document.getElementById('docAboutBio').value = doc.aboutBio || '';

    const modal = new bootstrap.Modal(document.getElementById('doctorModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
