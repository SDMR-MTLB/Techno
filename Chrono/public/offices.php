<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);

$db = DB::getConnection();
$stmt = $db->query("SELECT * FROM offices WHERE is_active = 1 ORDER BY city");
$offices = $stmt->fetchAll();

$pageTitle = 'Our Offices';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-map-marker-alt"></i> Our Office Locations
            </h2>
        </div>

        <?php if (count($offices) > 0): ?>
            <div class="services-grid"> <!-- reuse services-grid for consistent card layout -->
                <?php foreach ($offices as $office): ?>
                    <div class="service-card" style="text-align: center;">
                        <div class="service-icon" style="margin: 0 auto 1rem;">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="service-name"><?= htmlspecialchars($office['name']) ?></h3>
                        <p class="service-desc" style="margin-bottom: 0.5rem;"><?= htmlspecialchars($office['address']) ?></p>
                        <p class="service-desc" style="margin-bottom: 0.5rem;"><?= htmlspecialchars($office['city']) ?></p>
                        <p class="service-desc" style="margin-bottom: 0;">
                            <i class="fas fa-phone" style="color: var(--brand-accent);"></i>
                            <?= htmlspecialchars($office['contact'] ?? 'N/A') ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-secondary); text-align: center;">No office locations available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>