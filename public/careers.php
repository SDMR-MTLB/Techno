<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Careers';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-briefcase"></i> Join Our Team
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">
                We're always looking for talented individuals to join our growing team. Current openings:
            </p>
            <ul style="margin-left: 1.5rem; margin-bottom: 1rem; color: var(--text-secondary);">
                <li><strong>Network Technician</strong> – Full‑time, Zamboanga City</li>
                <li><strong>Customer Support Representative</strong> – Remote</li>
                <li><strong>Sales Associate</strong> – Zamboanga City</li>
            </ul>
            <p style="color: var(--text-secondary);">
                Send your resume to <a href="mailto:careers@nethub.com" style="color: var(--brand-accent);">careers@nethub.com</a>.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>