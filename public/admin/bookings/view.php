<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/bookings/index.php');
    exit;
}

$db = DB::getConnection();
$stmt = $db->prepare("
    SELECT b.*, u.name as customer_name, u.email, u.phone, u.address as user_address,
           o.name as office_name, o.address as office_address, o.city, o.contact as office_contact
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN offices o ON b.office_id = o.id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: ' . BASE_URL . '/admin/bookings/index.php');
    exit;
}

// Fetch related service/package/consultation details if any
$related = null;
if ($booking['booking_type'] == 'service' && $booking['service_id']) {
    $relStmt = $db->prepare("SELECT name, description, estimated_price FROM services WHERE id = ?");
    $relStmt->execute([$booking['service_id']]);
    $related = $relStmt->fetch();
} elseif ($booking['booking_type'] == 'package' && $booking['package_id']) {
    $relStmt = $db->prepare("SELECT name, description, price, inclusions FROM packages WHERE id = ?");
    $relStmt->execute([$booking['package_id']]);
    $related = $relStmt->fetch();
} elseif ($booking['booking_type'] == 'consultation' && $booking['consultation_id']) {
    $relStmt = $db->prepare("SELECT * FROM consultations WHERE id = ?");
    $relStmt->execute([$booking['consultation_id']]);
    $related = $relStmt->fetch();
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

    if (in_array($newStatus, ['pending', 'approved', 'rejected', 'completed'])) {
        $updateStmt = $db->prepare("UPDATE bookings SET status = ?, admin_notes = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $adminNotes, $id]);
        $booking['status'] = $newStatus;
        $booking['admin_notes'] = $adminNotes;
        $success = "Booking status updated.";
    }
}

$pageTitle = 'Booking Details';
$activePage = 'bookings';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>Booking #<?= htmlspecialchars($booking['booking_code']) ?></h2>

    <?php if (isset($success)): ?>
        <div style="background: rgba(46,204,113,0.2); padding: 10px; border-radius: 10px; margin-bottom: 20px;"><?= $success ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($booking['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($booking['user_address']) ?></p>
        </div>
        <div>
            <h3>Office Details</h3>
            <p><strong>Office:</strong> <?= htmlspecialchars($booking['office_name']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($booking['office_address']) ?></p>
            <p><strong>City:</strong> <?= htmlspecialchars($booking['city']) ?></p>
            <p><strong>Contact:</strong> <?= htmlspecialchars($booking['office_contact'] ?? 'N/A') ?></p>
        </div>
    </div>

    <div style="margin-top:20px;">
        <h3>Booking Details</h3>
        <p><strong>Type:</strong> <?= ucfirst($booking['booking_type']) ?></p>
        <p><strong>Preferred Date:</strong> <?= $booking['preferred_date'] ? date('Y-m-d', strtotime($booking['preferred_date'])) : 'Not specified' ?></p>
        <p><strong>Preferred Time:</strong> <?= $booking['preferred_time'] ?? 'Not specified' ?></p>
        <p><strong>Client Address for Service:</strong> <?= nl2br(htmlspecialchars($booking['client_address'])) ?></p>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($booking['description'] ?? '')) ?></p>
        <p><strong>Status:</strong> <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span></p>
    </div>

    <?php if ($related): ?>
        <div style="margin-top:20px;">
            <h3><?= ucfirst($booking['booking_type']) ?> Details</h3>
            <?php if ($booking['booking_type'] == 'service'): ?>
                <p><strong>Service:</strong> <?= htmlspecialchars($related['name']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($related['description'] ?? '')) ?></p>
                <p><strong>Price:</strong> ₱<?= number_format($related['estimated_price'], 2) ?></p>
            <?php elseif ($booking['booking_type'] == 'package'): ?>
                <p><strong>Package:</strong> <?= htmlspecialchars($related['name']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($related['description'] ?? '')) ?></p>
                <p><strong>Price:</strong> ₱<?= number_format($related['price'], 2) ?></p>
                <p><strong>Inclusions:</strong> <?= nl2br(htmlspecialchars($related['inclusions'] ?? '')) ?></p>
            <?php elseif ($booking['booking_type'] == 'consultation'): ?>
                <p><strong>Consultation ID:</strong> <?= $booking['consultation_id'] ?></p>
                <!-- More details can be shown -->
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <h3>Admin Notes</h3>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="form-group">
                <textarea name="admin_notes" rows="4"><?= htmlspecialchars($booking['admin_notes'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="status">Update Status</label>
                <select name="status" id="status">
                    <option value="pending" <?= $booking['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $booking['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $booking['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="completed" <?= $booking['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <button type="submit" name="update_status" class="admin-btn">Update</button>
            <a href="index.php" class="admin-btn admin-btn-small">Back to List</a>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>