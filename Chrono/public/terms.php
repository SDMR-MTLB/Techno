<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Terms of Service';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-file-contract"></i> Terms of Service
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Acceptance of Terms</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">By using our website and services, you agree to these terms.</p>

            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Booking and Payment</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">All bookings are subject to availability. Payment must be made as specified during checkout.</p>

            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Cancellations</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Cancellations made at least 24 hours before the scheduled service are fully refundable.</p>

            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Limitation of Liability</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Pisowifi Vendo is not liable for indirect damages arising from use of our services.</p>

            <p style="color: var(--text-secondary);">For full terms, please contact us.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>