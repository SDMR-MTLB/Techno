<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';

requireLogin();

$db = DB::getConnection();
$adminId = $_SESSION['admin_id'];

// Fetch product count per category
$catStmt = $db->prepare("SELECT category, COUNT(*) as count FROM products WHERE admin_id = ? AND status = 'available' GROUP BY category");
$catStmt->execute([$adminId]);
$categoryStats = $catStmt->fetchAll();

// Fetch total products and packages
$totalProducts = $db->prepare("SELECT COUNT(*) FROM products WHERE admin_id = ?");
$totalProducts->execute([$adminId]);
$productCount = $totalProducts->fetchColumn();

$totalPackages = $db->prepare("SELECT COUNT(*) FROM packages WHERE admin_id = ?");
$totalPackages->execute([$adminId]);
$packageCount = $totalPackages->fetchColumn();

$pageTitle = 'Reports';
$activePage = 'reports';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-semibold">Reports</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Overview of your product catalog.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $productCount ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-box text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Packages</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $packageCount ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-cubes text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<?php if ($categoryStats): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold">Products by Category</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Count</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($categoryStats as $row): ?>
                <tr class="table-row-hover">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($row['category'] ?: 'Uncategorized') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?= $row['count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
        <i class="fas fa-chart-pie text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-500 dark:text-gray-400">No products yet. Add some products to see reports.</p>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>