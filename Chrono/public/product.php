<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$db = DB::getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: products.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND status = 'available'");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = $product['name'];
include __DIR__ . '/includes/main-header.php';
?>

<style>
    /* Ensure the button styles are applied even if global styles are missing */
    a.btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--brand-primary, #1e3a5f), var(--brand-accent, #0ea5e9));
        color: white;
        text-decoration: none;
        border-radius: 9999px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        width: fit-content;
    }
    .dark a.btn-primary {
        color: #000;
    }
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--bg-tertiary);
        color: var(--text-primary);
        text-decoration: none;
        border-radius: 9999px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
        cursor: pointer;
        width: fit-content;
    }
    .btn-secondary:hover {
        background: var(--border-color);
    }
    .dark .btn-secondary:hover {
        background: var(--text-muted);
        color: white;
    }
</style>

<section class="section">
    <div class="container">
        <div class="product-detail" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <div>
                <?php if ($product['image']): ?>
                    <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%; border-radius: 1rem;">
                <?php else: ?>
                    <div style="background: var(--bg-tertiary); height: 300px; border-radius: 1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-microchip" style="font-size: 5rem; color: var(--text-muted);"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h1 style="font-size: 2rem; margin-bottom: 1rem; color: var(--text-primary);"><?= htmlspecialchars($product['name']) ?></h1>
                <p style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 1rem;"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 1.5rem;">₱<?= number_format($product['price'], 2) ?></p>
                
                <?php if ($product['affiliate_url']): ?>
                    <a href="<?= htmlspecialchars($product['affiliate_url']) ?>" target="_blank" class="btn-primary">
                        <i class="fas fa-external-link-alt"></i> Buy from Partner
                    </a>
                <?php else: ?>
                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">This product is available directly from us.</p>
                    <a href="<?= BASE_URL ?>/contact.php?subject=Inquiry about <?= urlencode($product['name']) ?>" class="btn-primary">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                <?php endif; ?>

                <p style="margin-top: 2rem;">
                    <a href="<?= BASE_URL ?>/products.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>