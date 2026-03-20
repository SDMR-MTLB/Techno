<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$db = DB::getConnection();
$booking = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['booking_code'] ?? '');
    if ($code) {
        $stmt = $db->prepare("SELECT b.*, u.name as customer_name, o.name as office_name 
                               FROM bookings b 
                               JOIN users u ON b.user_id = u.id 
                               JOIN offices o ON b.office_id = o.id 
                               WHERE b.booking_code = ?");
        $stmt->execute([$code]);
        $booking = $stmt->fetch();
        if (!$booking) {
            $error = "Booking code not found.";
        }
    }
}

$pageTitle = 'Track Booking';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-search"></i> Track Your Booking
            </h2>
        </div>

        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <form method="post">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Enter Booking Code</label>
                        <input type="text" name="booking_code" placeholder="e.g. BOK-20250301-0012" required 
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem;">
                        Track
                    </button>
                </form>

                <?php if ($error): ?>
                    <p style="color: #ef4444; margin-top: 1rem;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if ($booking): ?>
                    <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Booking Details</h3>
                        <p style="color: var(--text-secondary);"><strong>Code:</strong> <?= htmlspecialchars($booking['booking_code']) ?></p>
                        <p style="color: var(--text-secondary);"><strong>Customer:</strong> <?= htmlspecialchars($booking['customer_name']) ?></p>
                        <p style="color: var(--text-secondary);"><strong>Office:</strong> <?= htmlspecialchars($booking['office_name']) ?></p>
                        <p style="color: var(--text-secondary);"><strong>Type:</strong> <?= ucfirst($booking['booking_type']) ?></p>
                        <p style="color: var(--text-secondary);"><strong>Status:</strong> 
                            <span class="badge" style="background: <?= $booking['status'] == 'pending' ? '#f59e0b' : ($booking['status'] == 'approved' ? '#3b82f6' : ($booking['status'] == 'completed' ? '#22c55e' : '#ef4444')) ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </p>
                        <p style="color: var(--text-secondary);"><strong>Preferred Date:</strong> <?= $booking['preferred_date'] ?: 'Not specified' ?></p>
                        <p style="color: var(--text-secondary);"><strong>Description:</strong> <?= nl2br(htmlspecialchars($booking['description'] ?? '')) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>