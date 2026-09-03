<?php
require_once __DIR__ . '/inc/header.php';

$doctors = getData('doctors');
$staff   = getData('staff');
$team    = getData('team');
$inquiries = getData('contact_messages');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 text-dark">Welcome to Admin Dashboard</h2>
        <p class="text-muted m-0">Manage doctor profiles, nursing staff cards, team profiles, and website contact inquiries.</p>
    </div>
    <div>
        <a href="doctors.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Doctor</a>
        <a href="staff.php" class="btn btn-teal btn-sm text-white" style="background: var(--brand-teal);"><i class="bi bi-plus-lg me-1"></i> Add Staff</a>
        <a href="team.php" class="btn btn-dark btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Team Member</a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Doctor Cards</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($doctors) ?></h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="doctors.php" class="text-primary text-decoration-none font-weight-bold" style="font-size: 0.85rem;">Manage Doctors <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Staff Cards</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($staff) ?></h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="staff.php" class="text-success text-decoration-none font-weight-bold" style="font-size: 0.85rem;">Manage Staff <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
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

    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Contact Messages</span>
                    <h3 class="m-0 font-weight-bold text-dark"><?= count($inquiries) ?></h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                    <i class="bi bi-envelope-paper"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-muted" style="font-size: 0.85rem;">Sent to: lifecarenursing5@gmail.com</span>
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
