<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/validator.php';
require_once __DIR__ . '/../../../core/csrf.php';
require_once __DIR__ . '/../../../core/upload.php';

requireLogin();

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
    $affiliate_url = trim($_POST['affiliate_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'available';
    $imageFile = $_FILES['image'] ?? null;

    // Validation
    $err = validateRequired($name, 'Product name');
    if ($err) $errors[] = $err;
    $err = validateRequired($price, 'Price');
    if ($err) $errors[] = $err;
    elseif (!is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a positive number.';
    }
    if ($affiliate_url && !filter_var($affiliate_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Invalid affiliate URL format.';
    }

    $imageName = null;
    if ($imageFile && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageName = uploadImage($imageFile);
        if (!$imageName) {
            $errors[] = 'Image upload failed. Please check file type (JPEG/PNG/GIF) and size (max 2MB).';
        }
    }

    if (empty($errors)) {
        $db = DB::getConnection();
        $stmt = $db->prepare("INSERT INTO products (admin_id, name, description, price, affiliate_url, image, category, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['admin_id'], $name, $description, $price, $affiliate_url, $imageName, $category, $status])) {
            header('Location: ' . BASE_URL . '/admin/products/index.php?added=1');
            exit;
        } else {
            $errors[] = 'Database error.';
        }
    }
}

$pageTitle = 'Add Product';
$activePage = 'products';

include __DIR__ . '/../partials/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Add New Product</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Create a new product in your catalog.</p>
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

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price *</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="affiliate_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Affiliate URL</label>
                <input type="url" id="affiliate_url" name="affiliate_url" value="<?= htmlspecialchars($_POST['affiliate_url'] ?? '') ?>" placeholder="https://..."
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPEG, PNG, GIF up to 2MB</p>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="available" <?= (($_POST['status'] ?? 'available') == 'available') ? 'selected' : '' ?>>Available</option>
                    <option value="unavailable" <?= (($_POST['status'] ?? '') == 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/products/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">Add Product</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>