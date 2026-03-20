<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
$service = null;

if ($serviceId) {
    $db = DB::getConnection();
    $stmt = $db->prepare("SELECT id, name, description, estimated_price FROM services WHERE id = ? AND status = 'active'");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Service – Pisowifi Vendo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="page-container">
    <h1 class="page-title">Request Service</h1>

    <?php if ($serviceId && !$service): ?>
        <div class="empty-state" style="color: #ffaaaa;">Selected service not found or unavailable.</div>
    <?php endif; ?>

    <?php if ($service): ?>
        <div class="form-container" style="margin-bottom: 30px;">
            <div class="card-title"><?= htmlspecialchars($service['name']) ?></div>
            <div class="card-price">
                <?= $service['estimated_price'] ? 'Estimated Price: ₱' . number_format($service['estimated_price'], 2) : 'Price upon request' ?>
            </div>
            <p><?= nl2br(htmlspecialchars($service['description'] ?? '')) ?></p>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="submit-service-request.php" method="post">
            <?php if ($service): ?>
                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact Number *</label>
                <input type="text" id="contact" name="contact" required>
            </div>
            <div class="form-group">
                <label for="email">Email (optional)</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="location">Location / Address *</label>
                <textarea id="location" name="location" required></textarea>
            </div>
            <div class="form-group">
                <label for="issue">Description of Issue *</label>
                <textarea id="issue" name="issue" required></textarea>
            </div>
            <div class="form-group">
                <label for="preferred_date">Preferred Date/Time (optional)</label>
                <input type="datetime-local" id="preferred_date" name="preferred_date">
            </div>
            <div class="form-group">
                <label for="notes">Additional Notes (optional)</label>
                <textarea id="notes" name="notes"></textarea>
            </div>
            <button type="submit" class="btn">Submit Request</button>
        </form>
    </div>
</div>

<div class="bottom-logo">LOGO</div>

</body>
</html>