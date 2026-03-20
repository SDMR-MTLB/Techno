<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/users/index.php');
    exit;
}

$db = DB::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ' . BASE_URL . '/admin/users/index.php');
    exit;
}

// Fetch user's bookings
$bookingsStmt = $db->prepare("SELECT b.*, o.name as office_name FROM bookings b JOIN offices o ON b.office_id = o.id WHERE b.user_id = ? ORDER BY b.id DESC LIMIT 10");
$bookingsStmt->execute([$id]);
$bookings = $bookingsStmt->fetchAll();

// Fetch user's consultations
$consultStmt = $db->prepare("SELECT * FROM consultations WHERE user_id = ? ORDER BY id DESC LIMIT 10");
$consultStmt->execute([$id]);
$consultations = $consultStmt->fetchAll();

// Fetch user's subscriptions
$subsStmt = $db->prepare("SELECT * FROM subscriptions WHERE user_id = ? ORDER BY id DESC");
$subsStmt->execute([$id]);
$subscriptions = $subsStmt->fetchAll();

$pageTitle = 'User Details';
$activePage = 'users';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>User: <?= htmlspecialchars($user['name']) ?></h2>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
            <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($user['address'] ?? 'N/A')) ?></p>
        </div>
        <div>
            <p><strong>Registered:</strong> <?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></p>
            <p><strong>Last Updated:</strong> <?= date('Y-m-d H:i', strtotime($user['updated_at'])) ?></p>
        </div>
    </div>

    <h3 style="margin-top:30px;">Recent Bookings</h3>
    <?php if ($bookings): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Office</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['booking_code']) ?></td>
                        <td><?= ucfirst($b['booking_type']) ?></td>
                        <td><?= htmlspecialchars($b['office_name']) ?></td>
                        <td><?= date('Y-m-d', strtotime($b['created_at'])) ?></td>
                        <td><span class="status-badge status-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings yet.</p>
    <?php endif; ?>

    <h3 style="margin-top:30px;">Consultations</h3>
    <?php if ($consultations): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultations as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= ucfirst($c['consultation_type']) ?></td>
                        <td><?= htmlspecialchars(substr($c['description'], 0, 50)) ?>...</td>
                        <td><span class="status-badge status-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($c['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No consultations.</p>
    <?php endif; ?>

    <h3 style="margin-top:30px;">Subscriptions</h3>
    <?php if ($subscriptions): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plan</th>
                    <th>Start Date</th>
                    <th>Next Billing</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscriptions as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= ucfirst($s['plan']) ?></td>
                        <td><?= $s['start_date'] ?></td>
                        <td><?= $s['next_billing'] ?></td>
                        <td><span class="status-badge status-<?= $s['status'] ?>"><?= ucfirst($s['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No subscriptions.</p>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <a href="index.php" class="admin-btn admin-btn-small">← Back to Users</a>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>