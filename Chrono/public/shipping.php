<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';

$isLoggedIn = isset($_SESSION['user_id']);
$pageTitle = 'Shipping Policy';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-truck"></i> Shipping Policy
            </h2>
        </div>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Delivery Times</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Orders are typically processed within 1-2 business days. Shipping times depend on your location:</p>
            <ul style="margin-left: 1.5rem; margin-bottom: 1rem; color: var(--text-secondary);">
                <li>Metro Manila: 5-7 business days</li>
                <li>Luzon: 7-10 business days</li>
                <li>Visayas/Mindanao: 3-5 business days</li>
            </ul>

            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Shipping Costs</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Free shipping on orders over ₱5,000. Otherwise, a flat rate of ₱150 applies.</p>
            <p style="color: var(--text-secondary);">For on‑site services, travel fees may apply – these will be quoted during booking.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>