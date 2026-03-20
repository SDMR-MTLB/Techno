<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/db.php';

requireLogin();

$db = DB::getConnection();
$adminId = $_SESSION['admin_id']; // partner ID

// Partner-specific stats
$totalProducts = $db->prepare("SELECT COUNT(*) FROM products WHERE admin_id = ?");
$totalProducts->execute([$adminId]);
$productCount = $totalProducts->fetchColumn();

$totalPackages = $db->prepare("SELECT COUNT(*) FROM packages WHERE admin_id = ?");
$totalPackages->execute([$adminId]);
$packageCount = $totalPackages->fetchColumn();

// Recent products
$recentStmt = $db->prepare("SELECT id, name, price, status, image FROM products WHERE admin_id = ? ORDER BY id DESC LIMIT 5");
$recentStmt->execute([$adminId]);
$recentProducts = $recentStmt->fetchAll();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

include __DIR__ . '/partials/header.php';
?>

<!-- Page Header -->
<div class="mb-6 sm:mb-8">
    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Partner Dashboard</h1>
    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Welcome back! Here's an overview of your products and packages.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">
    <!-- Products Count -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 hover-lift transition-all-200 stat-animate" style="animation-delay: 0s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Your Products</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $productCount ?></p>
                <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                    <i class="fas fa-arrow-up"></i> Total listings
                </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-box text-blue-600 text-lg"></i>
            </div>
        </div>
    </div>
    
    <!-- Packages Count -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 hover-lift transition-all-200 stat-animate" style="animation-delay: 0.1s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Your Packages</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $packageCount ?></p>
                <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                    <i class="fas fa-arrow-up"></i> Active bundles
                </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-cubes text-purple-600 text-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mb-8">
    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <a href="<?= BASE_URL ?>/admin/products/add.php" class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 hover-lift transition-all-200 group">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center mb-3 group-hover:bg-primary-600 transition-all-200">
                <i class="fas fa-plus text-primary-600 group-hover:text-white transition-all-200 text-sm sm:text-base"></i>
            </div>
            <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">Add Product</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">New listing</p>
        </a>
        <a href="<?= BASE_URL ?>/admin/packages/add.php" class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 hover-lift transition-all-200 group">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-600 transition-all-200">
                <i class="fas fa-cubes text-purple-600 group-hover:text-white transition-all-200 text-sm sm:text-base"></i>
            </div>
            <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">Create Package</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bundle products</p>
        </a>       
    </div>
</div>

<!-- Recent Products Table -->
<?php if ($recentProducts): ?>
<div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Recent Products</h2>
        <a href="<?= BASE_URL ?>/admin/products.php" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($recentProducts as $product): ?>
                <tr class="table-row-hover transition-all-200">
                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 dark:bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-microchip text-gray-400"></i>
                                <?php endif; ?>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white text-sm sm:text-base"><?= htmlspecialchars($product['name']) ?></span>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600 dark:text-gray-300">₱<?= number_format($product['price'], 2) ?></td>
                    <td class="px-4 sm:px-6 py-4">
                        <?php if (($product['status'] ?? 'available') === 'available'): ?>
                            <span class="badge bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                        <?php else: ?>
                            <span class="badge bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= BASE_URL ?>/partner/product-edit.php?id=<?= $product['id'] ?>" class="p-1.5 sm:p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all-200" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <button class="p-1.5 sm:p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all-200" title="Delete">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="bg-white dark:bg-slate-800 rounded-xl p-8 text-center border border-gray-200 dark:border-gray-700">
    <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
    <p class="text-gray-500 dark:text-gray-400">You haven't added any products yet.</p>
    <a href="<?= BASE_URL ?>/partner/products-add.php" class="inline-block mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700 transition">Add Your First Product</a>
</div>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>