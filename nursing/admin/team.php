<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$type = 'team';
$team = getData($type);
$msg = '';
$error = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
    $team = array_filter($team, function($t) use ($deleteId) {
        return (int)$t['id'] !== $deleteId;
    });
    $team = array_values($team);
    saveData($type, $team);
    header('Location: team.php?msg=deleted');
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

    if (empty($name) || empty($role)) {
        $error = 'Team Member Name and Role are required.';
    } else {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        if ($id > 0) {
            foreach ($team as &$tm) {
                if ((int)$tm['id'] === $id) {
                    $tm['name']        = $name;
                    $tm['role']        = $role;
                    $tm['badge']       = $badge ?: 'Team';
                    $tm['description'] = $description;
                    $tm['whatsapp']    = $whatsapp;
                    if ($imagePath) {
                        $tm['image']   = $imagePath;
                    }
                    break;
                }
            }
            saveData($type, $team);
            header('Location: team.php?msg=updated');
            exit;
        } else {
            $newId = !empty($team) ? max(array_column($team, 'id')) + 1 : 1;
            $newMember = [
                'id'          => $newId,
                'name'        => $name,
                'role'        => $role,
                'badge'       => $badge ?: 'Team Member',
                'image'       => $imagePath ?: 'assets/doctor_1.jpg',
                'description' => $description,
                'whatsapp'    => $whatsapp
            ];
            $team[] = $newMember;
            saveData($type, $team);
            header('Location: team.php?msg=added');
            exit;
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $msg = 'New Team Member Card uploaded successfully!';
    if ($_GET['msg'] === 'updated') $msg = 'Team Member Card updated successfully!';
    if ($_GET['msg'] === 'deleted') $msg = 'Team Member Card removed successfully!';
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 text-dark">Our Team Cards Management</h2>
        <p class="text-muted m-0">Upload, edit, and manage team cards displayed on team.php and website about sections.</p>
    </div>
    <button type="button" class="btn-care" data-bs-toggle="modal" data-bs-target="#teamModal" onclick="resetTeamForm()">
        <i class="bi bi-person-plus-fill me-1"></i> Upload New Team Card
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

<!-- Team Horizontal Cards Grid -->
<div class="row g-3">
    <?php foreach ($team as $tm): ?>
        <div class="col-12 col-xl-6">
            <div class="card-custom p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative flex-shrink-0" style="width: 140px; height: 140px; border-radius: 12px; overflow: hidden; background: #eaf3f1;">
                        <img src="../<?= htmlspecialchars($tm['image']) ?>" alt="<?= htmlspecialchars($tm['name']) ?>" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                            <span class="badge text-white px-2.5 py-1" style="background: var(--brand-navy); font-size: 0.72rem; letter-spacing: 0.04em; text-transform: uppercase;">
                                <?= htmlspecialchars($tm['badge'] ?? 'Management') ?>
                            </span>
                            <span class="text-muted" style="font-size: 0.75rem;">ID: #<?= $tm['id'] ?></span>
                        </div>
                        <h5 class="m-0 font-weight-bold text-dark text-truncate"><?= htmlspecialchars($tm['name']) ?></h5>
                        <div class="font-weight-bold text-uppercase mb-1" style="color: var(--brand-teal); font-size: 0.78rem; letter-spacing: 0.03em;">
                            <?= htmlspecialchars($tm['role']) ?>
                        </div>
                        <p class="text-secondary mb-2" style="font-size: 0.84rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($tm['description']) ?>
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" onclick='editTeam(<?= json_encode($tm) ?>)'>
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                            <a href="team.php?action=delete&id=<?= $tm['id'] ?>" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Are you sure you want to delete this Team member card?');">
                                <i class="bi bi-trash-fill me-1"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add / Edit Team Modal -->
<div class="modal fade" id="teamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white" style="background: var(--brand-navy);">
                <h5 class="modal-title font-weight-bold" id="modalTitle"><i class="bi bi-diagram-3-fill me-2"></i> Upload New Team Member Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="tm_id" value="0">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Team Member Name *</label>
                            <input type="text" class="form-control" name="name" id="tm_name" placeholder="e.g. Dr. Haris Abbasi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Role / Designation *</label>
                            <input type="text" class="form-control" name="role" id="tm_role" placeholder="e.g. Medical Director, Supervisor" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Category Badge</label>
                            <input type="text" class="form-control" name="badge" id="tm_badge" placeholder="e.g. Management, Operations">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">WhatsApp Contact Number</label>
                            <input type="text" class="form-control" name="whatsapp" id="tm_whatsapp" placeholder="923008053198">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Upload Member Photo (JPG, PNG, WEBP)</label>
                            <input type="file" class="form-control" name="image" id="tm_image" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-uppercase text-secondary" style="font-size: 0.78rem;">Description / Responsibilities</label>
                            <textarea class="form-control" name="description" id="tm_description" rows="3" placeholder="Brief summary of duties and leadership role..."></textarea>
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
function resetTeamForm() {
    document.getElementById('modalTitle').innerText = 'Upload New Team Member Card';
    document.getElementById('tm_id').value = '0';
    document.getElementById('tm_name').value = '';
    document.getElementById('tm_role').value = '';
    document.getElementById('tm_badge').value = 'Management';
    document.getElementById('tm_whatsapp').value = '923008053198';
    document.getElementById('tm_description').value = '';
    document.getElementById('tm_image').value = '';
}

function editTeam(tm) {
    document.getElementById('modalTitle').innerText = 'Edit Team Member Card';
    document.getElementById('tm_id').value = tm.id;
    document.getElementById('tm_name').value = tm.name;
    document.getElementById('tm_role').value = tm.role;
    document.getElementById('tm_badge').value = tm.badge || '';
    document.getElementById('tm_whatsapp').value = tm.whatsapp || '923008053198';
    document.getElementById('tm_description').value = tm.description;
    
    var modal = new bootstrap.Modal(document.getElementById('teamModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
