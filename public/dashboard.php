<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/user_auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$isLoggedIn = true; // for header
$db = DB::getConnection();
$userId = $_SESSION['user_id'];

// Fetch user's bookings
$bookingsStmt = $db->prepare("SELECT b.*, o.name as office_name FROM bookings b JOIN offices o ON b.office_id = o.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
$bookingsStmt->execute([$userId]);
$bookings = $bookingsStmt->fetchAll();

// Fetch user's consultations
$consultsStmt = $db->prepare("SELECT * FROM consultations WHERE user_id = ? ORDER BY created_at DESC");
$consultsStmt->execute([$userId]);
$consultations = $consultsStmt->fetchAll();

// Fetch user's subscriptions
$subsStmt = $db->prepare("SELECT * FROM subscriptions WHERE user_id = ? ORDER BY id DESC");
$subsStmt->execute([$userId]);
$subscriptions = $subsStmt->fetchAll();

$pageTitle = 'My Dashboard';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-tachometer-alt"></i> Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>
            </h2>
        </div>

        <!-- Stats Cards - now 3 in a row -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="stat-value"><?= count($bookings) ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-headset"></i></div>
                <div>
                    <div class="stat-value"><?= count($consultations) ?></div>
                    <div class="stat-label">Consultations</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-sync-alt"></i></div>
                <div>
                    <div class="stat-value"><?= count($subscriptions) ?></div>
                    <div class="stat-label">Subscriptions</div>
                </div>
            </div>
        </div>

        <?php if ($bookings): ?>
            <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Recent Bookings</h3>
            <div class="table-responsive" style="background: var(--card-bg); border-radius: 1rem; padding: 1.5rem; box-shadow: var(--shadow);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary); font-weight: 600;">Code</th>
                            <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary); font-weight: 600;">Type</th>
                            <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary); font-weight: 600;">Office</th>
                            <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary); font-weight: 600;">Date</th>
                            <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary); font-weight: 600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem 0.5rem; color: var(--text-primary);"><?= htmlspecialchars($b['booking_code']) ?></td>
                            <td style="padding: 0.75rem 0.5rem; color: var(--text-primary);"><?= ucfirst($b['booking_type']) ?></td>
                            <td style="padding: 0.75rem 0.5rem; color: var(--text-primary);"><?= htmlspecialchars($b['office_name']) ?></td>
                            <td style="padding: 0.75rem 0.5rem; color: var(--text-primary);"><?= date('Y-m-d', strtotime($b['created_at'])) ?></td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: <?= $b['status'] == 'pending' ? '#f59e0b' : ($b['status'] == 'approved' ? '#3b82f6' : ($b['status'] == 'completed' ? '#22c55e' : '#ef4444')) ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    <?= ucfirst($b['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: var(--text-secondary); text-align: center; padding: 2rem; background: var(--card-bg); border-radius: 1rem;">You have no bookings yet.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>