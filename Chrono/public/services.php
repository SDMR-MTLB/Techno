<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$db = DB::getConnection();

$stmt = $db->query("SELECT * FROM services WHERE status = 'active' ORDER BY id DESC");
$services = $stmt->fetchAll();

$pageTitle = 'Services';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-tools"></i> Our Services
            </h2>
        </div>

        <?php if (count($services) > 0): ?>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3 class="service-name"><?= htmlspecialchars($service['name']) ?></h3>
                        <p class="service-desc"><?= htmlspecialchars($service['description'] ?? '') ?></p>

                        <div class="service-price-row" style="margin-top: 1rem;">
                            <div>
                                <div class="service-price-label">Price</div>
                                <div class="service-price">
                                    <?= $service['estimated_price'] ? '₱' . number_format($service['estimated_price'], 0) : 'Upon request' ?>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/booking-service.php?service_id=<?= $service['id'] ?>" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                Book Now <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-secondary); text-align: center;">No services available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>