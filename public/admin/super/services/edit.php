<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';
require_once __DIR__ . '/../../../../core/validator.php';
require_once __DIR__ . '/../../../../core/csrf.php';

requireSuperAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/super/services/index.php');
    exit;
}

$db = DB::getConnection();

// Fetch service – no admin_id condition
$stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: ' . BASE_URL . '/admin/super/services/index.php');
    exit;
}

$errors = [];
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    clearCsrfToken();

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $estimated_price = trim($_POST['estimated_price'] ?? '');
    $status = $_POST['status'] ?? 'active';

    $err = validateRequired($name, 'Service name');
    if ($err) $errors[] = $err;
    if ($estimated_price && (!is_numeric($estimated_price) || $estimated_price < 0)) {
        $errors[] = 'Estimated price must be a positive number.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE services SET name=?, description=?, estimated_price=?, status=? WHERE id=?");
        if ($stmt->execute([$name, $description, $estimated_price ?: null, $status, $id])) {
            header('Location: ' . BASE_URL . '/admin/super/services/index.php?updated=1');
            exit;
        } else {
            $errors[] = 'Database error.';
        }
    }
}

$pageTitle = 'Edit Service';
$activePage = 'super_services';

include __DIR__ . '/../../partials/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Edit Service</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Update service details.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <?php if (!empty($errors)): ?>
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $service['name']) ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200"><?= htmlspecialchars($_POST['description'] ?? $service['description']) ?></textarea>
            </div>

            <div class="mb-4">
                <label for="estimated_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimated Price</label>
                <input type="number" step="0.01" id="estimated_price" name="estimated_price" value="<?= htmlspecialchars($_POST['estimated_price'] ?? $service['estimated_price']) ?>"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
                    <option value="active" <?= (($_POST['status'] ?? $service['status']) === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (($_POST['status'] ?? $service['status']) === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    <option value="deleted" <?= (($_POST['status'] ?? $service['status']) === 'deleted') ? 'selected' : '' ?>>Deleted</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/super/services/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">Update Service</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>