<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'About Us';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-building"></i> About Us
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">
                NetHub is a leading provider of networking solutions in the Philippines. We specialize in high‑quality hardware, professional configuration services, and complete installation packages for small offices and home offices (SOHO).
            </p>
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">
                Our team of certified technicians ensures that your network runs smoothly, whether you need a simple router setup or a full‑scale office deployment.
            </p>
            <p style="color: var(--text-secondary);">
                We are committed to providing excellent customer service and reliable products at competitive prices.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>