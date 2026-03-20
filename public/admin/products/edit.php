<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/validator.php';
require_once __DIR__ . '/../../../core/csrf.php';
require_once __DIR__ . '/../../../core/upload.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/products/index.php');
    exit;
}

$db = DB::getConnection();
$adminId = $_SESSION['admin_id'];
$isSuper = ($_SESSION['admin_role'] ?? '') === 'super';

// Verify ownership (unless super)
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?" . ($isSuper ? "" : " AND admin_id = ?"));
if ($isSuper) {
    $stmt->execute([$id]);
} else {
    $stmt->execute([$id, $adminId]);
}
$product = $stmt->fetch();

if (!$product) {
    header('Location: ' . BASE_URL . '/admin/products/index.php');
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
    $price = trim($_POST['price'] ?? '');
    $affiliate_url = trim($_POST['affiliate_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'available';
    $imageFile = $_FILES['image'] ?? null;
    $removeImage = isset($_POST['remove_image']);

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

    $imageName = $product['image']; // keep old by default

    if ($removeImage) {
        if ($product['image']) {
            deleteImage($product['image']);
        }
        $imageName = null;
    } elseif ($imageFile && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
        $newImage = uploadImage($imageFile);
        if ($newImage) {
            if ($product['image']) {
                deleteImage($product['image']);
            }
            $imageName = $newImage;
        } else {
            $errors[] = 'Image upload failed.';
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE products SET name=?, description=?, price=?, affiliate_url=?, image=?, category=?, status=? WHERE id=?");
        if ($stmt->execute([$name, $description, $price, $affiliate_url, $imageName, $category, $status, $id])) {
            header('Location: ' . BASE_URL . '/admin/products/index.php?updated=1');
            exit;
        } else {
            $errors[] = 'Database error.';
        }
    }
}

$pageTitle = 'Edit Product';
$activePage = 'products';

include __DIR__ . '/../partials/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Edit Product</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Update product details.</p>
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
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $product['name']) ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?= htmlspecialchars($_POST['description'] ?? $product['description']) ?></textarea>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price *</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="affiliate_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Affiliate URL</label>
                <input type="url" id="affiliate_url" name="affiliate_url" value="<?= htmlspecialchars($_POST['affiliate_url'] ?? $product['affiliate_url']) ?>" placeholder="https://..."
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($_POST['category'] ?? $product['category']) ?>"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Image</label>
                <?php if ($product['image']): ?>
                    <div class="flex items-center gap-4 mb-2">
                        <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="" class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="remove_image" value="1"> Remove image
                        </label>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No image currently.</p>
                <?php endif; ?>
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload New Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="available" <?= (($_POST['status'] ?? $product['status']) == 'available') ? 'selected' : '' ?>>Available</option>
                    <option value="unavailable" <?= (($_POST['status'] ?? $product['status']) == 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
                    <option value="deleted" <?= (($_POST['status'] ?? $product['status']) == 'deleted') ? 'selected' : '' ?>>Deleted</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/products/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all-200">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">Update Product</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>