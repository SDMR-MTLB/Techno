<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireSuperAdmin();

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (!isset($_GET['token']) || !validateCsrfToken($_GET['token'])) {
        die('Invalid CSRF token.');
    }
    clearCsrfToken();
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/super/messages.php?deleted=1');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$totalStmt = $db->query("SELECT COUNT(*) FROM contact_messages");
$totalMessages = $totalStmt->fetchColumn();
$totalPages = ceil($totalMessages / $limit);

$stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();

$pageTitle = 'Contact Messages';
$activePage = 'messages';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-semibold">Contact Messages</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">View and manage customer inquiries.</p>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-green-600 dark:text-green-400">
        Message deleted successfully.
    </div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <?php if (count($messages) > 0): ?>
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Received</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($messages as $msg): ?>
                <tr class="table-row-hover">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= $msg['id'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($msg['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($msg['subject'] ?? '-') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= date('Y-m-d H:i', strtotime($msg['created_at'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $msg['is_read'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' ?>">
                            <?= $msg['is_read'] ? 'Read' : 'Unread' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?= BASE_URL ?>/admin/super/message-view.php?id=<?= $msg['id'] ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="?delete=<?= $msg['id'] ?>&token=<?= urlencode($csrfToken) ?>" 
                           class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                           onclick="return confirm('Delete this message?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="px-3 py-1 rounded-lg <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600' ?> transition-all-200">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p>No messages yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>