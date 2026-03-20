<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);

$db = DB::getConnection();
$stmt = $db->query("SELECT * FROM packages WHERE status = 'active' ORDER BY id DESC");
$packages = $stmt->fetchAll();

$pageTitle = 'Installation Packages';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-box"></i> SOHO Installation Packages
            </h2>
        </div>

        <?php if (count($packages) > 0): ?>
            <div class="services-grid"> <!-- reuse services-grid for consistent card layout -->
                <?php foreach ($packages as $package): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h3 class="service-name"><?= htmlspecialchars($package['name']) ?></h3>
                        <p class="service-desc"><?= htmlspecialchars($package['description'] ?? '') ?></p>
                        
                        <div class="service-price-row" style="margin-top: 1rem;">
                            <div>
                                <div class="service-price-label">Price</div>
                                <div class="service-price">₱<?= number_format($package['price'], 0) ?></div>
                            </div>
                            <a href="<?= BASE_URL ?>/booking-package.php?package_id=<?= $package['id'] ?>" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                Request <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-secondary); text-align: center;">No packages available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>