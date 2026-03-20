<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireSuperAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/super/messages.php');
    exit;
}

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

// Fetch the message
$stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    header('Location: ' . BASE_URL . '/admin/super/messages.php');
    exit;
}

// Mark as read when viewed
if (!$message['is_read']) {
    $updateStmt = $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $updateStmt->execute([$id]);
    $message['is_read'] = 1;
}

$pageTitle = 'View Message';
$activePage = 'messages';

include __DIR__ . '/../partials/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Message from <?= htmlspecialchars($message['name']) ?></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Received on <?= date('F j, Y, g:i a', strtotime($message['created_at'])) ?></p>
        </div>
        <a href="<?= BASE_URL ?>/admin/super/messages.php" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">
            <i class="fas fa-arrow-left"></i> Back to Messages
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-700">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($message['name']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</dt>
                    <dd class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($message['email']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subject</dt>
                    <dd class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($message['subject'] ?? '(no subject)') ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</dt>
                    <dd class="text-sm">
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $message['is_read'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' ?>">
                            <?= $message['is_read'] ? 'Read' : 'Unread' ?>
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Message:</h2>
            <div class="bg-gray-50 dark:bg-slate-700 p-4 rounded-lg border-l-4 border-primary-500">
                <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
            </div>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <a href="?delete=<?= $message['id'] ?>&token=<?= urlencode($csrfToken) ?>" 
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all-200"
               onclick="return confirm('Delete this message?')">
                <i class="fas fa-trash mr-2"></i> Delete Message
            </a>
        </div>
    </div>
</div>

<?php
// Handle delete from this page
if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (!isset($_GET['token']) || !validateCsrfToken($_GET['token'])) {
        die('Invalid CSRF token.');
    }
    clearCsrfToken();
    $did = (int)$_GET['delete'];
    $dstmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
    $dstmt->execute([$did]);
    header('Location: ' . BASE_URL . '/admin/super/messages.php?deleted=1');
    exit;
}

include __DIR__ . '/../partials/footer.php';
?>