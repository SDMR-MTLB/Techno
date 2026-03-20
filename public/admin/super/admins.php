<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireSuperAdmin();

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

// Fetch all admins with role 'partner'
$stmt = $db->query("SELECT id, username, created_at FROM admins WHERE role = 'partner' ORDER BY id DESC");
$admins = $stmt->fetchAll();

$pageTitle = 'Manage Partner Admins';
$activePage = 'admins';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-semibold">Manage Partner Admins</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Create, edit, or remove partner store accounts.</p>
</div>

<div class="mb-4 flex justify-end">
    <a href="<?= BASE_URL ?>/admin/super/admins-add.php" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-all-200">
        <i class="fas fa-plus"></i> Add New Partner
    </a>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-600">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($admins as $admin): ?>
            <tr class="table-row-hover">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= $admin['id'] ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($admin['username']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= date('Y-m-d', strtotime($admin['created_at'])) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="<?= BASE_URL ?>/admin/super/admins-edit.php?id=<?= $admin['id'] ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?= BASE_URL ?>/admin/super/admins-delete.php?id=<?= $admin['id'] ?>&token=<?= urlencode($csrfToken) ?>" 
                       class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                       onclick="return confirm('Delete this partner admin? This will also delete all their products and packages.')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>