<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Press';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-newspaper"></i> Press Releases
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <article style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 0.25rem;">NetHub Launches New SOHO Installation Packages</h3>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">March 1, 2025</p>
                <p style="color: var(--text-secondary);">We're excited to announce our new all‑in‑one installation packages for small offices, including hardware, configuration, and on‑site setup.</p>
            </article>
            <article style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 0.25rem;">Company Expands to Cebu</h3>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">January 15, 2025</p>
                <p style="color: var(--text-secondary);">NetHub opens a new office in Cebu to better serve Visayas customers.</p>
            </article>
            <p style="color: var(--text-secondary);">For media inquiries, contact <a href="mailto:press@nethub.ph" style="color: var(--brand-accent);">press@nethub.ph</a>.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>