<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$requestId = $_GET['id'] ?? '';
$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Request Confirmed';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto; background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="section-title" style="font-size: 2rem; justify-content: center;">Thank You!</h1>
            <p style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 20px;">Your request has been submitted successfully.</p>
            <?php if ($requestId): ?>
                <p style="margin-bottom: 20px; color: var(--text-secondary);">Your request ID is: <strong><?= htmlspecialchars($requestId) ?></strong></p>
            <?php endif; ?>
            <p style="margin-bottom: 30px; color: var(--text-secondary);">We will contact you shortly.</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn-primary">Return to Home</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>