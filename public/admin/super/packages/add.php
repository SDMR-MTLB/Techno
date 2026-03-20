<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';
require_once __DIR__ . '/../../../../core/validator.php';
require_once __DIR__ . '/../../../../core/csrf.php';

requireSuperAdmin();

$errors = [];
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    clearCsrfToken();

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $inclusions = trim($_POST['inclusions'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $adminId = $_SESSION['admin_id'];

    $err = validateRequired($name, 'Package name');
    if ($err) $errors[] = $err;
    if ($price && (!is_numeric($price) || $price < 0)) {
        $errors[] = 'Price must be a positive number.';
    }

    if (empty($errors)) {
        $db = DB::getConnection();
        $stmt = $db->prepare("INSERT INTO packages (admin_id, name, description, price, inclusions, status) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$adminId, $name, $description, $price ?: null, $inclusions, $status])) {
            header('Location: ' . BASE_URL . '/admin/super/packages/index.php?added=1');
            exit;
        } else {
            $errors[] = 'Database error.';
        }
    }
}

$pageTitle = 'Add Package';
$activePage = 'super_packages';

include __DIR__ . '/../../partials/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Add New Package</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Create a new package.</p>
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
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Package Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="mb-4">
                <label for="inclusions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inclusions</label>
                <textarea id="inclusions" name="inclusions" rows="3"
                          class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200"><?= htmlspecialchars($_POST['inclusions'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
                    <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/super/packages/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">Add Package</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>