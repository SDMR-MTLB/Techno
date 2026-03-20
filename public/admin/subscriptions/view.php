<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/subscriptions/index.php');
    exit;
}

$db = DB::getConnection();
$stmt = $db->prepare("SELECT s.*, u.name as user_name, u.email, u.phone, u.address 
                      FROM subscriptions s
                      JOIN users u ON s.user_id = u.id
                      WHERE s.id = ?");
$stmt->execute([$id]);
$subscription = $stmt->fetch();

if (!$subscription) {
    header('Location: ' . BASE_URL . '/admin/subscriptions/index.php');
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
    if (in_array($newStatus, ['active', 'paused', 'cancelled'])) {
        $updateStmt = $db->prepare("UPDATE subscriptions SET status = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $id]);
        $subscription['status'] = $newStatus;
        $success = "Subscription status updated.";
    }
}

$pageTitle = 'Subscription Details';
$activePage = 'subscriptions';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>Subscription #<?= $subscription['id'] ?></h2>

    <?php if (isset($success)): ?>
        <div style="background: rgba(46,204,113,0.2); padding: 10px; border-radius: 10px; margin-bottom: 20px;"><?= $success ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h3>User Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($subscription['user_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($subscription['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($subscription['phone'] ?? 'N/A') ?></p>
            <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($subscription['address'] ?? 'N/A')) ?></p>
        </div>
        <div>
            <h3>Subscription Details</h3>
            <p><strong>Plan:</strong> <?= ucfirst($subscription['plan']) ?></p>
            <p><strong>Start Date:</strong> <?= $subscription['start_date'] ?></p>
            <p><strong>Next Billing:</strong> <?= $subscription['next_billing'] ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?= $subscription['status'] ?>"><?= ucfirst($subscription['status']) ?></span></p>
            <p><strong>Created:</strong> <?= date('Y-m-d H:i', strtotime($subscription['created_at'])) ?></p>
        </div>
    </div>

    <div style="margin-top:20px;">
        <h3>Update Status</h3>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="form-group">
                <select name="status">
                    <option value="active" <?= $subscription['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="paused" <?= $subscription['status'] == 'paused' ? 'selected' : '' ?>>Paused</option>
                    <option value="cancelled" <?= $subscription['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" name="update_status" class="admin-btn">Update Status</button>
            <a href="index.php" class="admin-btn admin-btn-small">Back to List</a>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>