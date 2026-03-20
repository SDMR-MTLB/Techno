<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Returns Policy';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-undo-alt"></i> Returns Policy
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">30-Day Return Guarantee</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">We want you to be completely satisfied with your purchase. If you're not happy, you can return most items within 30 days of delivery for a full refund or exchange.</p>

            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Conditions</h3>
            <ul style="margin-left: 1.5rem; margin-bottom: 1rem; color: var(--text-secondary);">
                <li>Items must be unused and in original packaging.</li>
                <li>Proof of purchase is required.</li>
                <li>Some products (like custom-configured hardware) may not be returnable.</li>
            </ul>

            <p style="color: var(--text-secondary);">Contact our support team to initiate a return.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>