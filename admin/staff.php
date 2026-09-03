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
    $badge       = trim($_POST['badge'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $whatsapp    = trim($_POST['whatsapp'] ?? '923008053198');

    if (empty($name) || empty($role) || empty($badge)) {
        $error = 'Staff Name, Role, and Specialization Badge are required.';
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
                    $stf['badge']       = $badge;
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
                'badge'       => $badge,
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
    if ($_GET['msg'] === 'added') $msg = 'New Nursing Staff Card uploaded and published successfully!';
    if ($_GET['msg'] === 'updated') $msg = 'Staff Card updated successfully!';
    if ($_GET['msg'] === 'deleted') $msg = 'Staff Card removed successfully!';
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 text-dark">Staff Panel Cards Management</h2>
        <p class="text-muted m-0">Upload, edit, and manage nursing staff cards displayed on staff-panel.php and website.</p>
    </div>
    <button type="button" class="btn-care" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="resetStaffForm()">
        <i class="bi bi-person-plus-fill me-1"></i> Upload New Staff Card
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

<!-- Staff Horizontal Cards Grid -->
<div class="row g-3">
    <?php foreach ($staff as $stf): ?>
        <div class="col-12 col-xl-6">
            <div class="card-custom p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative flex-shrink-0" style="width: 140px; height: 140px; border-radius: 12px; overflow: hidden; background: #eaf3f1;">
                        <img src="../<?= htmlspecialchars($stf['image']) ?>" alt="<?= htmlspecialchars($stf['name']) ?>" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                            <span class="badge text-white px-2.5 py-1" style="background: var(--brand-navy); font-size: 0.72rem; letter-spacing: 0.04em; text-transform: uppercase;">
                                <?= htmlspecialchars($stf['badge']) ?>
                            </span>
                            <span class="text-muted" style="font-size: 0.75rem;">ID: #<?= $stf['id'] ?></span>
                        </div>
                        <h5 class="m-0 font-weight-bold text-dark text-truncate"><?= htmlspecialchars($stf['name']) ?></h5>
                        <div class="font-weight-bold text-uppercase mb-1" style="color: var(--brand-teal); font-size: 0.78rem; letter-spacing: 0.03em;">
                            <?= htmlspecialchars($stf['role']) ?>
                        </div>
                        <p class="text-secondary mb-2" style="font-size: 0.84rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($stf['description']) ?>
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" onclick='editStaff(<?= json_encode($stf) ?>)'>
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                            <a href="staff.php?action=delete&id=<?= $stf['id'] ?>" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Are you sure you want to delete this Staff card?');">
                                <i class="bi bi-trash-fill me-1"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add / Edit Staff Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white" style="background: var(--brand-navy);">
                <h5 class="modal-title font-weight-bold" id="modalTitle"><i class="bi bi-people-fill me-2"></i> Upload New Staff Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="stf_id" value="0">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Staff Member Name *</label>
                            <input type="text" class="form-control" name="name" id="stf_name" placeholder="e.g. James N., Maria K." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Role / Title *</label>
                            <input type="text" class="form-control" name="role" id="stf_role" placeholder="e.g. Senior Registered Nurse, Caregiver" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Specialty Badge *</label>
                            <input type="text" class="form-control" name="badge" id="stf_badge" placeholder="e.g. ICU Nurse, Elderly Care, Attendant" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">WhatsApp Contact Number</label>
                            <input type="text" class="form-control" name="whatsapp" id="stf_whatsapp" placeholder="923008053198">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Upload Staff Photo (JPG, PNG, WEBP)</label>
                            <input type="file" class="form-control" name="image" id="stf_image" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Staff Experience / Description</label>
                            <textarea class="form-control" name="description" id="stf_description" rows="3" placeholder="Description of nursing skills, experience, ventilator/patient care capabilities..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-care px-4"><i class="bi bi-cloud-upload-fill me-1"></i> Save & Publish Card</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetStaffForm() {
    document.getElementById('modalTitle').innerText = 'Upload New Staff Card';
    document.getElementById('stf_id').value = '0';
    document.getElementById('stf_name').value = '';
    document.getElementById('stf_role').value = '';
    document.getElementById('stf_badge').value = '';
    document.getElementById('stf_whatsapp').value = '923008053198';
    document.getElementById('stf_description').value = '';
    document.getElementById('stf_image').value = '';
}

function editStaff(stf) {
    document.getElementById('modalTitle').innerText = 'Edit Staff Card';
    document.getElementById('stf_id').value = stf.id;
    document.getElementById('stf_name').value = stf.name;
    document.getElementById('stf_role').value = stf.role;
    document.getElementById('stf_badge').value = stf.badge;
    document.getElementById('stf_whatsapp').value = stf.whatsapp || '923008053198';
    document.getElementById('stf_description').value = stf.description;
    
    var modal = new bootstrap.Modal(document.getElementById('staffModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
