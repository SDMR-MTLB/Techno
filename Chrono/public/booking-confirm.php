<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$db = DB::getConnection();

$code = $_GET['code'] ?? '';
if (!$code) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Fetch booking details
$stmt = $db->prepare("
    SELECT b.*, 
           u.name as customer_name, 
           o.name as office_name,
           s.name as service_name,
           p.name as package_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN offices o ON b.office_id = o.id
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN packages p ON b.package_id = p.id
    WHERE b.booking_code = ?
");
$stmt->execute([$code]);
$booking = $stmt->fetch();

if (!$booking) {
    // Booking not found – redirect to home or show error
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$pageTitle = 'Booking Confirmed';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow); text-align: center;">
                <div style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 style="font-size: 2rem; margin-bottom: 1rem; color: var(--text-primary);">Thank You!</h1>
                <p style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 2rem;">
                    Your booking has been submitted successfully.
                </p>

                <!-- Booking Code Highlight -->
                <div style="background: var(--bg-primary); border: 2px dashed var(--brand-accent); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; text-align: center;">
                    <p style="font-size: 1rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Your Booking Reference</p>
                    <p style="font-size: 2.5rem; font-weight: 700; color: var(--brand-primary); letter-spacing: 2px; word-break: break-word;">
                        <?= htmlspecialchars($booking['booking_code']) ?>
                    </p>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 0.5rem;">
                        Please save this code to track your booking.
                    </p>
                </div>

                <div style="background: var(--bg-tertiary); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; text-align: left;">
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Booking Details</h3>
                    <p style="color: var(--text-secondary);"><strong>Customer:</strong> <?= htmlspecialchars($booking['customer_name']) ?></p>
                    <p style="color: var(--text-secondary);"><strong>Office:</strong> <?= htmlspecialchars($booking['office_name']) ?></p>
                    <p style="color: var(--text-secondary);"><strong>Type:</strong> <?= ucfirst($booking['booking_type']) ?></p>
                    <?php if ($booking['booking_type'] == 'service' && $booking['service_name']): ?>
                        <p style="color: var(--text-secondary);"><strong>Service:</strong> <?= htmlspecialchars($booking['service_name']) ?></p>
                    <?php elseif ($booking['booking_type'] == 'package' && $booking['package_name']): ?>
                        <p style="color: var(--text-secondary);"><strong>Package:</strong> <?= htmlspecialchars($booking['package_name']) ?></p>
                    <?php endif; ?>
                    <p style="color: var(--text-secondary);"><strong>Preferred Date:</strong> <?= $booking['preferred_date'] ?: 'Not specified' ?></p>
                    <p style="color: var(--text-secondary);"><strong>Status:</strong> 
                        <span class="badge" style="background: #f59e0b; color: white; padding: 0.25rem 1rem; border-radius: 9999px;">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </p>
                </div>

                <p style="margin-bottom: 2rem; color: var(--text-secondary);">
                    We will review your booking and contact you shortly.<br>
                    You can track your booking status using the code above.
                </p>

                <div class="btn-group" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="<?= BASE_URL ?>/track.php?code=<?= urlencode($booking['booking_code']) ?>" class="btn-primary">
                        <i class="fas fa-search"></i> Track Booking
                    </a>
                    <a href="<?= BASE_URL ?>/index.php" class="btn-primary" style="background: var(--bg-tertiary); color: var(--text-primary);">
                        <i class="fas fa-home"></i> Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>