<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Privacy Policy';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-lock"></i> Privacy Policy
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.</p>
            
            <h3 style="color: var(--text-primary); margin: 1.5rem 0 0.5rem;">Information We Collect</h3>
            <p style="color: var(--text-secondary);">We collect name, email, phone, address, and payment details when you register or make a booking.</p>
            
            <h3 style="color: var(--text-primary); margin: 1.5rem 0 0.5rem;">How We Use Information</h3>
            <p style="color: var(--text-secondary);">We use your information to process bookings, communicate with you, and improve our services.</p>
            
            <h3 style="color: var(--text-primary); margin: 1.5rem 0 0.5rem;">Data Security</h3>
            <p style="color: var(--text-secondary);">We implement industry‑standard security measures to protect your data.</p>
            
            <p style="color: var(--text-secondary); margin-top: 1rem;">For full details, contact us.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>