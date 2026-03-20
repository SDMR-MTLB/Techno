<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$db = DB::getConnection();
$csrfToken = generateCsrfToken();
$adminId = $_SESSION['admin_id'];
$isSuper = ($_SESSION['admin_role'] ?? '') === 'super';

// Pagination and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Base query
$sql = "SELECT * FROM packages WHERE 1";
$params = [];

if (!$isSuper) {
    $sql .= " AND admin_id = ?";
    $params[] = $adminId;
}
if ($search) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statusFilter && in_array($statusFilter, ['active', 'inactive', 'deleted'])) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

// Count total (without pagination)
$countSql = "SELECT COUNT(*) FROM packages WHERE 1";
$countParams = [];

if (!$isSuper) {
    $countSql .= " AND admin_id = ?";
    $countParams[] = $adminId;
}
if ($search) {
    $countSql .= " AND (name LIKE ? OR description LIKE ?)";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
}
if ($statusFilter && in_array($statusFilter, ['active', 'inactive', 'deleted'])) {
    $countSql .= " AND status = ?";
    $countParams[] = $statusFilter;
}

$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalPackages = $countStmt->fetchColumn();
$totalPages = ceil($totalPackages / $limit);

// Main query with pagination
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$packages = $stmt->fetchAll();

$pageTitle = 'Manage Packages';
$activePage = 'packages';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Manage Packages</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Create and manage your service packages.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/packages/add.php" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-all-200">
        <i class="fas fa-plus"></i> Add New Package
    </a>
</div>

<!-- Filter Form -->
<div class="mb-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <form method="get" class="flex flex-wrap gap-3">
        <input type="text" name="search" placeholder="Search packages..." value="<?= htmlspecialchars($search) ?>"
               class="flex-1 min-w-[200px] px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
        <select name="status" class="px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            <option value="">All Status</option>
            <option value="active" <?= $statusFilter == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $statusFilter == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="deleted" <?= $statusFilter == 'deleted' ? 'selected' : '' ?>>Deleted</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">Filter</button>
        <a href="<?= BASE_URL ?>/admin/packages/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Clear</a>
    </form>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <?php if (count($packages) > 0): ?>
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Inclusions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($packages as $pkg): ?>
                <tr class="table-row-hover">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= $pkg['id'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($pkg['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">₱<?= number_format($pkg['price'], 2) ?></td>
                    <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars(substr($pkg['inclusions'] ?? '', 0, 50)) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $pkg['status'] == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                               ($pkg['status'] == 'inactive' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                               'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400') ?>">
                            <?= ucfirst($pkg['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?= BASE_URL ?>/admin/packages/edit.php?id=<?= $pkg['id'] ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <?php if ($pkg['status'] !== 'deleted'): ?>
                            <a href="<?= BASE_URL ?>/admin/packages/delete.php?id=<?= $pkg['id'] ?>&token=<?= urlencode($csrfToken) ?>" 
                               class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                               onclick="return confirm('Deactivate this package?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/admin/packages/delete.php?id=<?= $pkg['id'] ?>&token=<?= urlencode($csrfToken) ?>&activate=1" 
                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                <i class="fas fa-undo"></i> Activate
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" 
                       class="px-3 py-1 rounded-lg <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600' ?> transition-all-200">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-box-open text-4xl mb-3"></i>
            <p>No packages found.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>