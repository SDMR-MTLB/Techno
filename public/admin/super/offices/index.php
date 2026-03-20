<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';
require_once __DIR__ . '/../../../../core/csrf.php';

requireSuperAdmin();

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM offices WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (name LIKE ? OR address LIKE ? OR city LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$offices = $stmt->fetchAll();

// Count total
$countSql = "SELECT COUNT(*) FROM offices WHERE 1";
$countParams = [];
if ($search) {
    $countSql .= " AND (name LIKE ? OR address LIKE ? OR city LIKE ?)";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
}
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalOffices = $countStmt->fetchColumn();
$totalPages = ceil($totalOffices / $limit);

$pageTitle = 'Manage Offices';
$activePage = 'super_offices';

include __DIR__ . '/../../partials/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-semibold">Offices</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Manage service locations.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/super/offices/add.php" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-all-200">
        <i class="fas fa-plus"></i> Add New Office
    </a>
</div>

<form method="get" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" placeholder="Search offices..." value="<?= htmlspecialchars($search) ?>" 
           class="flex-1 min-w-[200px] px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-all-200">Search</button>
    <?php if ($search): ?>
        <a href="<?= BASE_URL ?>/admin/super/offices/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Clear</a>
    <?php endif; ?>
</form>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <?php if (count($offices) > 0): ?>
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($offices as $office): ?>
                <tr class="table-row-hover">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= $office['id'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($office['name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($office['address']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($office['city']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($office['contact'] ?? '-') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $office['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' ?>">
                            <?= $office['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
						<a href="<?= BASE_URL ?>/admin/super/offices/edit.php?id=<?= $office['id'] ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3" title="Edit">
							<i class="fas fa-edit"></i>
						</a>
						<?php if ($office['is_active']): ?>
						<a href="<?= BASE_URL ?>/admin/super/offices/delete.php?id=<?= $office['id'] ?>&token=<?= urlencode($csrfToken) ?>" 
							class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
							onclick="return confirm('Deactivate this office?')" title="Deactivate">
							<i class="fas fa-trash"></i>
						</a>
						<?php else: ?>
							<a href="<?= BASE_URL ?>/admin/super/offices/delete.php?id=<?= $office['id'] ?>&token=<?= urlencode($csrfToken) ?>&activate=1" 
							   class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="Activate">
								<i class="fas fa-check"></i>
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
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                       class="px-3 py-1 rounded-lg <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600' ?> transition-all-200">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-building text-4xl mb-3"></i>
            <p>No offices found.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>