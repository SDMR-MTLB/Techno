<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/validator.php';
require_once __DIR__ . '/../../../core/csrf.php';

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
    $estimated_price = trim($_POST['estimated_price'] ?? '');
    $status = $_POST['status'] ?? 'active';

    $err = validateRequired($name, 'Service name');
    if ($err) $errors[] = $err;

    if ($estimated_price !== '' && (!is_numeric($estimated_price) || $estimated_price < 0)) {
        $errors[] = 'Estimated price must be a positive number.';
    }

    if (empty($errors)) {
        $db = DB::getConnection();
        $stmt = $db->prepare("INSERT INTO services (name, description, estimated_price, status) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $estimated_price ?: null, $status])) {
            header('Location: ' . BASE_URL . '/admin/services/index.php?added=1');
            exit;
        } else {
            $errors[] = 'Database error.';
        }
    }
}

$pageTitle = 'Add Service';
$activePage = 'services';

include __DIR__ . '/../partials/header.php';
?>

<div class="card admin-form">
    <h2>Add New Service</h2>

    <?php if (!empty($errors)): ?>
        <div style="background: rgba(214,48,49,0.2); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="form-group">
            <label for="name">Service Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="estimated_price">Estimated Price (optional)</label>
            <input type="number" step="0.01" id="estimated_price" name="estimated_price" value="<?= htmlspecialchars($_POST['estimated_price'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active" <?= (($_POST['status'] ?? '') == 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= (($_POST['status'] ?? '') == 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="btn-group">
            <button type="submit" class="admin-btn">Add Service</button>
            <a href="<?= BASE_URL ?>/admin/services/index.php" class="admin-btn admin-btn-small">Cancel</a>
        </div>
    </form>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>