<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Frequently Asked Questions';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-question-circle"></i> Frequently Asked Questions
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <div style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">How do I book a service?</h3>
                <p style="color: var(--text-secondary);">Simply browse our services, click "Book Now", and follow the steps. You'll need to be logged in to complete the booking.</p>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">What is your return policy?</h3>
                <p style="color: var(--text-secondary);">We offer a 30-day return policy on hardware products. For services, please refer to our service terms.</p>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Do you offer on‑site support?</h3>
                <p style="color: var(--text-secondary);">Yes, many of our services include on‑site visits. You can select your preferred office during booking.</p>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">How can I track my booking?</h3>
                <p style="color: var(--text-secondary);">Use our <a href="<?= BASE_URL ?>/track.php" style="color: var(--brand-accent);">Track Booking</a> page with your booking code.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>