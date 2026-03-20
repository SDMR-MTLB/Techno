<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';
require_once __DIR__ . '/../../../../core/csrf.php';

requireSuperAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/super/bookings/index.php');
    exit;
}

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

// Fetch booking with details
$stmt = $db->prepare("
    SELECT b.*, 
           u.name as customer_name, u.email, u.phone, u.address as user_address,
           o.name as office_name, o.address as office_address, o.city, o.contact as office_contact
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN offices o ON b.office_id = o.id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: ' . BASE_URL . '/admin/super/bookings/index.php');
    exit;
}

// Fetch related service/package details if applicable
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
$activePage = 'super_bookings';

include __DIR__ . '/../../partials/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Booking <?= htmlspecialchars($booking['booking_code']) ?></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">View and manage booking details.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/super/bookings/index.php" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if (isset($success)): ?>
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-green-600 dark:text-green-400">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-700">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking Code</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($booking['booking_code']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</dt>
                    <dd class="text-sm text-gray-900 dark:text-white"><?= ucfirst($booking['booking_type']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</dt>
                    <dd class="text-sm">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $booking['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                ($booking['status'] == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                ($booking['status'] == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400')) ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Created</dt>
                    <dd class="text-sm text-gray-500 dark:text-gray-400"><?= date('F j, Y, g:i a', strtotime($booking['created_at'])) ?></dd>
                </div>
            </dl>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Customer Info -->
            <div>
                <h3 class="text-lg font-medium mb-3">Customer</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($booking['customer_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($booking['phone'] ?? 'N/A') ?></p>
                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($booking['user_address'] ?? '')) ?></p>
            </div>
            <!-- Office Info -->
            <div>
                <h3 class="text-lg font-medium mb-3">Office</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($booking['office_name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($booking['office_address']) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($booking['city']) ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($booking['office_contact'] ?? 'N/A') ?></p>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium mb-3">Booking Details</h3>
            <p><strong>Preferred Date:</strong> <?= $booking['preferred_date'] ? date('Y-m-d', strtotime($booking['preferred_date'])) : 'Not specified' ?></p>
            <p><strong>Preferred Time:</strong> <?= $booking['preferred_time'] ?? 'Not specified' ?></p>
            <p><strong>Client Address for Service:</strong> <?= nl2br(htmlspecialchars($booking['client_address'])) ?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($booking['description'] ?? '')) ?></p>
        </div>

        <?php if ($related): ?>
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium mb-3"><?= ucfirst($booking['booking_type']) ?> Details</h3>
            <?php if ($booking['booking_type'] == 'service'): ?>
                <p><strong>Service:</strong> <?= htmlspecialchars($related['name']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($related['description'] ?? '')) ?></p>
                <p><strong>Estimated Price:</strong> <?= $related['estimated_price'] ? '₱'.number_format($related['estimated_price'],2) : 'Upon request' ?></p>
            <?php elseif ($booking['booking_type'] == 'package'): ?>
                <p><strong>Package:</strong> <?= htmlspecialchars($related['name']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($related['description'] ?? '')) ?></p>
                <p><strong>Price:</strong> <?= $related['price'] ? '₱'.number_format($related['price'],2) : 'N/A' ?></p>
                <p><strong>Inclusions:</strong> <?= nl2br(htmlspecialchars($related['inclusions'] ?? '')) ?></p>
            <?php elseif ($booking['booking_type'] == 'consultation'): ?>
                <p><strong>Consultation ID:</strong> <?= $booking['consultation_id'] ?></p>
                <p><strong>Type:</strong> <?= ucfirst($related['consultation_type'] ?? '') ?></p>
                <p><strong>Business Type:</strong> <?= htmlspecialchars($related['business_type'] ?? '') ?></p>
                <p><strong>Existing Setup:</strong> <?= nl2br(htmlspecialchars($related['existing_setup'] ?? '')) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium mb-3">Admin Notes & Status</h3>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <div class="mb-4">
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin Notes</label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" 
                              class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?= htmlspecialchars($booking['admin_notes'] ?? '') ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Update Status</label>
                    <select id="status" name="status" 
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="pending" <?= $booking['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $booking['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $booking['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="completed" <?= $booking['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">
                    Update Booking
                </button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>