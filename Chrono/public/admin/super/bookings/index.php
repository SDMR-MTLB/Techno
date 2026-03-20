<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';

requireSuperAdmin();

$db = DB::getConnection();

// Pagination & filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT b.*, u.name as customer_name, o.name as office_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN offices o ON b.office_id = o.id
        WHERE 1";
$params = [];

if ($typeFilter) {
    $sql .= " AND b.booking_type = ?";
    $params[] = $typeFilter;
}
if ($statusFilter) {
    $sql .= " AND b.status = ?";
    $params[] = $statusFilter;
}
if ($search) {
    $sql .= " AND (b.booking_code LIKE ? OR u.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$countSql = "SELECT COUNT(*) FROM bookings b WHERE 1";
$countParams = $params;
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalBookings = $countStmt->fetchColumn();
$totalPages = ceil($totalBookings / $limit);

$sql .= " ORDER BY b.id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$pageTitle = 'Manage Bookings';
$activePage = 'super_bookings';

include __DIR__ . '/../../partials/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-semibold">Bookings</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">View and manage all bookings.</p>
</div>

<form method="get" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" placeholder="Search by code or customer..." value="<?= htmlspecialchars($search) ?>" 
           class="flex-1 min-w-[200px] px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    <select name="type" class="px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        <option value="">All Types</option>
        <option value="service" <?= $typeFilter == 'service' ? 'selected' : '' ?>>Service</option>
        <option value="package" <?= $typeFilter == 'package' ? 'selected' : '' ?>>Package</option>
        <option value="consultation" <?= $typeFilter == 'consultation' ? 'selected' : '' ?>>Consultation</option>
    </select>
    <select name="status" class="px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        <option value="">All Status</option>
        <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $statusFilter == 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $statusFilter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        <option value="completed" <?= $statusFilter == 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-all-200">Filter</button>
    <a href="<?= BASE_URL ?>/admin/super/bookings/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Clear</a>
</form>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <?php if (count($bookings) > 0): ?>
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Office</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Preferred Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($bookings as $b): ?>
                <tr class="table-row-hover">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($b['booking_code']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($b['customer_name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= ucfirst($b['booking_type']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($b['office_name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= $b['preferred_date'] ? date('Y-m-d', strtotime($b['preferred_date'])) : '-' ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $b['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                ($b['status'] == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                ($b['status'] == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400')) ?>">
                            <?= ucfirst($b['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?= BASE_URL ?>/admin/super/bookings/view.php?id=<?= $b['id'] ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&type=<?= urlencode($typeFilter) ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>" 
                       class="px-3 py-1 rounded-lg <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600' ?> transition-all-200">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-calendar-check text-4xl mb-3"></i>
            <p>No bookings found.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>