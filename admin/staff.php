<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$type = 'staff';
$staff = getData($type);
$msg = '';
$error = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
    $staff = array_filter($staff, function($s) use ($deleteId) {
        return (int)$s['id'] !== $deleteId;
    });
    $staff = array_values($staff);
    saveData($type, $staff);
    header('Location: staff.php?msg=deleted');
    exit;
}

// Handle Add / Edit POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name        = trim($_POST['name'] ?? '');
    $role        = trim($_POST['role'] ?? '');
    $city        = trim($_POST['city'] ?? 'Faisalabad');
    $badge       = trim($_POST['badge'] ?? 'Registered Nurse');
    $shift       = trim($_POST['shift'] ?? '12-Hour Shift');
    $rate        = trim($_POST['rate'] ?? 'Rs. 2,200 / Day');
    $skillsRaw   = trim($_POST['skills'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $whatsapp    = trim($_POST['whatsapp'] ?? '923008053198');

    $skillsArr = array_filter(array_map('trim', explode(',', $skillsRaw)));
    if (empty($skillsArr)) {
        $skillsArr = ['Patient Care', 'Vitals Tracking', 'Emergency Support'];
    }

    if (empty($name) || empty($role)) {
        $error = 'Staff Name and Role are required.';
    } else {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        if ($id > 0) {
            foreach ($staff as &$stf) {
                if ((int)$stf['id'] === $id) {
                    $stf['name']        = $name;
                    $stf['role']        = $role;
                    $stf['city']        = $city;
                    $stf['badge']       = $badge;
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
            saveData($type, $staff);
            header('Location: staff.php?msg=updated');
            exit;
        } else {
            $newId = !empty($staff) ? max(array_column($staff, 'id')) + 1 : 1;
            $newStaff = [
                'id'          => $newId,
                'name'        => $name,
                'role'        => $role,
                'city'        => $city,
                'badge'       => $badge,
                'skills'      => array_values($skillsArr),
                'shift'       => $shift,
                'rate'        => $rate,
                'image'       => $imagePath ?: 'assets/staff_1.jpg',
                'description' => $description,
                'whatsapp'    => $whatsapp
            ];
            $staff[] = $newStaff;
            saveData($type, $staff);
            header('Location: staff.php?msg=added');
            exit;
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $msg = 'New Staff Profile created successfully!';
    if ($_GET['msg'] === 'updated') $msg = 'Staff Profile updated successfully!';
    if ($_GET['msg'] === 'deleted') $msg = 'Staff Profile removed successfully!';
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 text-dark">Staff Panel Directory Management</h2>
        <p class="text-muted m-0">Manage nursing staff profiles, city assignments, skills tags, shift availability, and service rates.</p>
    </div>
    <button type="button" class="btn-care" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="resetStaffForm()">
        <i class="bi bi-person-plus-fill me-1"></i> Add New Staff Member
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
        <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-people-fill text-success me-2"></i> Registered Staff (<?= count($staff) ?>)</h5>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th>Staff Name & Role</th>
                    <th>City / Location</th>
                    <th>Skills / Services</th>
                    <th>Shift & Rate</th>
                    <th class="text-end" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staff)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No staff profiles found. Click "Add New Staff Member" to create one.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($staff as $stf): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($stf['image']) ?>" alt="Photo" class="thumb-preview" onerror="this.src='../assets/staff_1.jpg'">
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($stf['name']) ?></div>
                                <div class="text-muted" style="font-size: 0.82rem;"><?= htmlspecialchars($stf['role']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> <?= htmlspecialchars($stf['city'] ?? 'Faisalabad') ?></span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                    <?php 
                                    $skillsList = is_array($stf['skills'] ?? null) ? $stf['skills'] : explode(',', $stf['skills'] ?? 'Patient Care');
                                    foreach (array_slice($skillsList, 0, 3) as $sk): 
                                    ?>
                                        <span class="badge bg-light text-dark border" style="font-size: 0.72rem;"><?= htmlspecialchars(trim($sk)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.82rem;"><?= htmlspecialchars($stf['shift'] ?? '12-Hour') ?></div>
                                <div class="text-teal small fw-bold" style="color:var(--brand-teal);"><?= htmlspecialchars($stf['rate'] ?? 'Rs. 2,200 / Day') ?></div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editStaff(<?= htmlspecialchars(json_encode($stf)) ?>)" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="staff.php?action=delete&id=<?= $stf['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this staff profile?');" title="Delete">
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

<!-- ADD / EDIT STAFF MODAL -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white p-4" style="background: var(--teal-900);">
                <h4 class="modal-title font-weight-bold m-0" id="staffModalTitle">
                    <i class="bi bi-person-plus-fill me-2" style="color: var(--amber-500);"></i> Add New Staff Member
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #FAF8F3;">
                <form method="POST" enctype="multipart/form-data" id="staffForm">
                    <input type="hidden" name="id" id="stfId" value="0">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="stfName" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Staff Full Name *</label>
                            <input type="text" class="form-control" name="name" id="stfName" placeholder="e.g. Tasawar Razzaq" required>
                        </div>
                        <div class="col-md-6">
                            <label for="stfCity" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">City / Location *</label>
                            <select class="form-select" name="city" id="stfCity">
                                <option value="Faisalabad">Faisalabad</option>
                                <option value="Lahore">Lahore</option>
                                <option value="Islamabad">Islamabad</option>
                                <option value="Rawalpindi">Rawalpindi</option>
                                <option value="Multan">Multan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="stfRole" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Role / Designation *</label>
                            <input type="text" class="form-control" name="role" id="stfRole" placeholder="e.g. Healthcare Assistant / Patient Care" required>
                        </div>
                        <div class="col-md-6">
                            <label for="stfBadge" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Category Badge</label>
                            <input type="text" class="form-control" name="badge" id="stfBadge" placeholder="e.g. Patient Care / ICU Nurse" value="Registered Nurse">
                        </div>

                        <div class="col-md-6">
                            <label for="stfShift" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Shift Type</label>
                            <input type="text" class="form-control" name="shift" id="stfShift" placeholder="e.g. 12-Hour Shift / 24-Hour Residential" value="12-Hour Shift">
                        </div>
                        <div class="col-md-6">
                            <label for="stfRate" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Daily / Service Rate</label>
                            <input type="text" class="form-control" name="rate" id="stfRate" placeholder="e.g. Rs. 2,200 / Day" value="Rs. 2,200 / Day">
                        </div>

                        <div class="col-12">
                            <label for="stfSkills" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Skills & Services (Comma Separated)</label>
                            <input type="text" class="form-control" name="skills" id="stfSkills" placeholder="e.g. Patient Care & Bedside Assistance, Operation Theatre Support, Vitals Tracking">
                            <div class="form-text">Enter skill tags separated by commas.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="stfImage" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Staff Photo</label>
                            <input type="file" class="form-control" name="image" id="stfImage" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label for="stfWhatsapp" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">WhatsApp Number</label>
                            <input type="text" class="form-control" name="whatsapp" id="stfWhatsapp" placeholder="923008053198" value="923008053198">
                        </div>

                        <div class="col-12">
                            <label for="stfDescription" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Description & Biography</label>
                            <textarea class="form-control" name="description" id="stfDescription" rows="3" placeholder="Overview of experience, specialty, and services..."></textarea>
                        </div>

                        <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-care px-4" style="border: none; cursor: pointer;">
                                <i class="bi bi-check-circle-fill me-1"></i> Save Staff Member
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetStaffForm() {
    document.getElementById('staffModalTitle').innerHTML = '<i class="bi bi-person-plus-fill me-2" style="color: var(--amber-500);"></i> Add New Staff Member';
    document.getElementById('stfId').value = 0;
    document.getElementById('staffForm').reset();
}

function editStaff(stf) {
    document.getElementById('staffModalTitle').innerHTML = '<i class="bi bi-pencil-square me-2" style="color: var(--amber-500);"></i> Edit Staff Profile';
    document.getElementById('stfId').value = stf.id;
    document.getElementById('stfName').value = stf.name || '';
    document.getElementById('stfCity').value = stf.city || 'Faisalabad';
    document.getElementById('stfRole').value = stf.role || '';
    document.getElementById('stfBadge').value = stf.badge || 'Registered Nurse';
    document.getElementById('stfShift').value = stf.shift || '12-Hour Shift';
    document.getElementById('stfRate').value = stf.rate || 'Rs. 2,200 / Day';
    
    const skillsList = is_array(stf.skills) ? stf.skills.join(', ') : (stf.skills || '');
    document.getElementById('stfSkills').value = skillsList;
    
    document.getElementById('stfWhatsapp').value = stf.whatsapp || '923008053198';
    document.getElementById('stfDescription').value = stf.description || '';

    const modal = new bootstrap.Modal(document.getElementById('staffModal'));
    modal.show();
}

function is_array(val) {
    return Array.isArray(val);
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
