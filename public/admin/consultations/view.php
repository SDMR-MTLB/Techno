<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/consultations/index.php');
    exit;
}

$db = DB::getConnection();
$stmt = $db->prepare("SELECT c.*, u.name as user_name, u.email, u.phone, u.address 
                      FROM consultations c 
                      LEFT JOIN users u ON c.user_id = u.id 
                      WHERE c.id = ?");
$stmt->execute([$id]);
$consultation = $stmt->fetch();

if (!$consultation) {
    header('Location: ' . BASE_URL . '/admin/consultations/index.php');
    exit;
}

$csrfToken = generateCsrfToken();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    clearCsrfToken();

    $newStatus = $_POST['status'] ?? '';
    $adminNotes = trim($_POST['admin_notes'] ?? '');

    if (in_array($newStatus, ['pending', 'reviewed', 'converted', 'rejected'])) {
        $updateStmt = $db->prepare("UPDATE consultations SET status = ?, admin_notes = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $adminNotes, $id]);
        // Refresh data
        $consultation['status'] = $newStatus;
        $consultation['admin_notes'] = $adminNotes;
        $success = "Status updated.";
    }
}

$pageTitle = 'Consultation Details';
$activePage = 'consultations';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>Consultation #<?= $consultation['id'] ?></h2>

    <?php if (isset($success)): ?>
        <div style="background: rgba(46,204,113,0.2); padding: 10px; border-radius: 10px; margin-bottom: 20px;"><?= $success ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($consultation['user_name'] ?? 'Guest') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($consultation['email'] ?? 'N/A') ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($consultation['phone'] ?? 'N/A') ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($consultation['address'] ?? 'N/A') ?></p>
        </div>
        <div>
            <h3>Consultation Details</h3>
            <p><strong>Type:</strong> <?= ucfirst($consultation['consultation_type']) ?></p>
            <p><strong>Business Type:</strong> <?= htmlspecialchars($consultation['business_type'] ?? 'N/A') ?></p>
            <p><strong>Preferred Schedule:</strong> <?= $consultation['preferred_schedule'] ? date('Y-m-d H:i', strtotime($consultation['preferred_schedule'])) : 'Not specified' ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?= $consultation['status'] ?>"><?= ucfirst($consultation['status']) ?></span></p>
        </div>
    </div>

    <div style="margin-top:20px;">
        <h3>Description of Issue</h3>
        <p><?= nl2br(htmlspecialchars($consultation['description'])) ?></p>
    </div>

    <?php if ($consultation['existing_setup']): ?>
        <div style="margin-top:20px;">
            <h3>Existing Setup</h3>
            <p><?= nl2br(htmlspecialchars($consultation['existing_setup'])) ?></p>
        </div>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <h3>Admin Notes</h3>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="form-group">
                <textarea name="admin_notes" rows="4"><?= htmlspecialchars($consultation['admin_notes'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="status">Update Status</label>
                <select name="status" id="status">
                    <option value="pending" <?= $consultation['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="reviewed" <?= $consultation['status'] == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                    <option value="converted" <?= $consultation['status'] == 'converted' ? 'selected' : '' ?>>Converted</option>
                    <option value="rejected" <?= $consultation['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" name="update_status" class="admin-btn">Update</button>
            <a href="index.php" class="admin-btn admin-btn-small">Back to List</a>
        </form>
    </div>

    <!-- Future: Button to convert to booking -->
    <div style="margin-top:20px;">
        <a href="<?= BASE_URL ?>/admin/bookings/add.php?consultation_id=<?= $consultation['id'] ?>" class="admin-btn admin-btn-small">Convert to Booking</a>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>