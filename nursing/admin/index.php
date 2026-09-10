<?php
require_once __DIR__ . '/inc/header.php';

$doctors = getData('doctors');
$staff   = getData('staff');
$team    = getData('team');
$blogs   = getData('blogs');
$inquiries = getData('contact_messages');

$pendingDoctors = array_filter($doctors, function($d) {
    return isset($d['status']) && $d['status'] === 'pending';
});
$pendingStaff = array_filter($staff, function($s) {
    return isset($s['status']) && $s['status'] === 'pending';
});
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="m-0 text-dark">Main Admin Control Center</h2>
        <p class="text-muted m-0">Oversee doctor & staff self-registrations, review approvals, manage content, and view patient inquiries.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="doctors.php" class="btn btn-primary btn-sm"><i class="bi bi-person-check me-1"></i> Review Doctors</a>
        <a href="staff.php" class="btn btn-teal btn-sm text-white" style="background: var(--brand-teal);"><i class="bi bi-person-check me-1"></i> Review Staff</a>
        <a href="blogs.php" class="btn btn-warning btn-sm text-dark font-weight-bold"><i class="bi bi-journal-plus me-1"></i> Add Blog</a>
    </div>
</div>

<?php if (count($pendingDoctors) > 0 || count($pendingStaff) > 0): ?>
    <div class="alert alert-warning border border-warning shadow-sm rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-dark p-2 rounded-circle fs-4">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div>
                <h6 class="m-0 font-weight-bold text-dark fs-6">Pending Registration Requests Needing Approval</h6>
                <p class="m-0 text-muted small">
                    <?php if (count($pendingDoctors) > 0): ?>
                        <strong><?= count($pendingDoctors) ?> Doctor(s)</strong>
                    <?php endif; ?>
                    <?php if (count($pendingDoctors) > 0 && count($pendingStaff) > 0): ?> &bull; <?php endif; ?>
                    <?php if (count($pendingStaff) > 0): ?>
                        <strong><?= count($pendingStaff) ?> Staff Member(s)</strong>
                    <?php endif; ?>
                    awaiting admin approval.
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if (count($pendingDoctors) > 0): ?>
                <a href="doctors.php" class="btn btn-sm btn-dark"><i class="bi bi-person-badge me-1"></i> Approve Doctors</a>
            <?php endif; ?>
            <?php if (count($pendingStaff) > 0): ?>
                <a href="staff.php" class="btn btn-sm btn-dark"><i class="bi bi-people me-1"></i> Approve Staff</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg">
        <div class="card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Total Doctors</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($doctors) ?></h3>
                    <?php if (count($pendingDoctors) > 0): ?>
                        <span class="badge bg-warning text-dark mt-1"><?= count($pendingDoctors) ?> Pending Approval</span>
                    <?php endif; ?>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="doctors.php" class="text-primary text-decoration-none font-weight-bold" style="font-size: 0.85rem;">Manage & Approve Doctors <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg">
        <div class="card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Total Staff</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($staff) ?></h3>
                    <?php if (count($pendingStaff) > 0): ?>
                        <span class="badge bg-warning text-dark mt-1"><?= count($pendingStaff) ?> Pending Approval</span>
                    <?php endif; ?>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="staff.php" class="text-success text-decoration-none font-weight-bold" style="font-size: 0.85rem;">Manage & Approve Staff <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg">
        <div class="card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Team Cards</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($team) ?></h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                    <i class="bi bi-diagram-3"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="team.php" class="text-info text-decoration-none font-weight-bold" style="font-size: 0.85rem;">Manage Team <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg">
        <div class="card-custom p-3 border-start border-4 border-teal" style="border-left-color: var(--brand-teal) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Blog Articles</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($blogs) ?></h3>
                </div>
                <div class="p-3 rounded-circle fs-3" style="background: rgba(2,148,145,0.15); color: var(--brand-teal);">
                    <i class="bi bi-journal-richtext"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="blogs.php" class="text-decoration-none font-weight-bold" style="color: var(--brand-teal); font-size: 0.85rem;">Manage Blogs <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg">
        <div class="card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Inquiries</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($inquiries) ?></h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                    <i class="bi bi-envelope-paper"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-muted" style="font-size: 0.85rem;">Form Submissions</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Inquiries Table -->
<div class="card-custom">
    <div class="card-custom-header">
        <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-inbox-fill text-warning me-2"></i> Recent Contact Us Form Submissions</h5>
        <span class="badge bg-secondary">Target Email: lifecarenursing5@gmail.com</span>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Phone / WhatsApp</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inquiries)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No contact messages received yet. Form submissions will appear here and send to lifecarenursing5@gmail.com.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach (array_slice($inquiries, 0, 10) as $msg): ?>
                        <tr>
                            <td class="text-muted" style="white-space: nowrap;"><?= htmlspecialchars($msg['date'] ?? 'N/A') ?></td>
                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($msg['name'] ?? '') ?></td>
                            <td><a href="tel:<?= htmlspecialchars($msg['phone'] ?? '') ?>" class="text-decoration-none fw-semibold"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($msg['phone'] ?? '') ?></a></td>
                            <td><a href="mailto:<?= htmlspecialchars($msg['email'] ?? '') ?>" class="text-decoration-none"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($msg['email'] ?? '') ?></a></td>
                            <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($msg['service'] ?? 'General') ?></span></td>
                            <td class="text-secondary" style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= htmlspecialchars($msg['message'] ?? '') ?>">
                                <?= htmlspecialchars($msg['message'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
