<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$db = DB::getConnection();

// Pagination and filtering
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Build search string with wildcards for BOOLEAN MODE
$searchBoolean = '';
if (!empty($search)) {
    $words = preg_split('/\s+/', trim($search));
    $words = array_filter($words, 'strlen');
    $words = array_map(function($word) {
        return $word . '*';
    }, $words);
    $searchBoolean = implode(' ', $words);
}

// Base query
$sql = "SELECT * FROM products WHERE status = 'available'";
$countSql = "SELECT COUNT(*) FROM products WHERE status = 'available'";
$params = [];
$countParams = [];

// Apply FULLTEXT search with BOOLEAN MODE if we have terms
if (!empty($searchBoolean)) {
    $sql .= " AND MATCH(name, description) AGAINST(? IN BOOLEAN MODE)";
    $countSql .= " AND MATCH(name, description) AGAINST(? IN BOOLEAN MODE)";
    $params[] = $searchBoolean;
    $countParams[] = $searchBoolean;
}

// Category filter
if (!empty($category)) {
    $sql .= " AND category = ?";
    $countSql .= " AND category = ?";
    $params[] = $category;
    $countParams[] = $category;
}

// Get total count for pagination
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Add ordering and pagination to main query
if (!empty($searchBoolean)) {
    $sql .= " ORDER BY MATCH(name, description) AGAINST(? IN BOOLEAN MODE) DESC, id DESC";
    $params[] = $searchBoolean;
} else {
    $sql .= " ORDER BY id DESC";
}
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'Products';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-box"></i>
                <?= $category ? 'Products in "' . htmlspecialchars(ucfirst(str_replace('-', ' ', $category))) . '"' : 'All Products' ?>
            </h2>
        </div>

        <!-- Search/Filter Form -->
        <form method="get" class="filter-form" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <?php if ($category): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
            <?php endif; ?>
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" 
                   style="flex: 1; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
            <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">Search</button>
            <?php if ($search || $category): ?>
                <a href="<?= BASE_URL ?>/products.php" class="btn-primary" style="background: var(--bg-tertiary); color: var(--text-primary);">Clear Filters</a>
            <?php endif; ?>
        </form>

        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <span class="product-badge">Available</span>
                        <button class="product-wishlist"><i class="far fa-heart"></i></button>
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <i class="fas fa-microchip" style="font-size: 3rem; color: var(--text-muted);"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-desc"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 40)) ?>...</p>
                            <div class="product-price">
                                <span class="current-price">₱<?= number_format($product['price'], 0) ?></span>
                            </div>
                            <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn-add-cart">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; gap: 0.5rem; margin: 2rem 0;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>" 
                           class="btn-primary" 
                           style="<?= $i == $page ? '' : 'background: var(--bg-tertiary); color: var(--text-primary);' ?> padding: 0.5rem 1rem;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p style="color: var(--text-secondary); text-align: center; padding: 3rem;">No products found.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>