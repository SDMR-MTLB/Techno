<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/validator.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/offices/index.php');
    exit;
}

$db = DB::getConnection();
$stmt = $db->prepare("SELECT * FROM offices WHERE id = ?");
$stmt->execute([$id]);
$office = $stmt->fetch();

if (!$office) {
    header('Location: ' . BASE_URL . '/admin/offices/index.php');
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
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $err = validateRequired($name, 'Office name');
    if ($err) $errors[] = $err;
    $err = validateRequired($address, 'Address');
    if ($err) $errors[] = $err;
    $err = validateRequired($city, 'City');
    if ($err) $errors[] = $err;

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE offices SET name=?, address=?, city=?, contact=?, is_active=? WHERE id=?");
        if ($stmt->execute([$name, $address, $city, $contact, $is_active, $id])) {
            header('Location: ' . BASE_URL . '/admin/offices/index.php?updated=1');
            exit;
        } else {
            $errors[] = 'Database error.';
        }
    }
}

$pageTitle = 'Edit Office';
$activePage = 'offices';

include __DIR__ . '/../partials/header.php';
?>

<div class="card admin-form">
    <h2>Edit Office: <?= htmlspecialchars($office['name']) ?></h2>

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
            <label for="name">Office Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $office['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Address *</label>
            <textarea id="address" name="address" required><?= htmlspecialchars($_POST['address'] ?? $office['address']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="city">City *</label>
            <input type="text" id="city" name="city" value="<?= htmlspecialchars($_POST['city'] ?? $office['city']) ?>" required>
        </div>

        <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($_POST['contact'] ?? $office['contact']) ?>">
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" <?= (($_POST['is_active'] ?? $office['is_active']) == 1) ? 'checked' : '' ?>> Active
            </label>
        </div>

        <div class="btn-group">
            <button type="submit" class="admin-btn">Update Office</button>
            <a href="<?= BASE_URL ?>/admin/offices/index.php" class="admin-btn admin-btn-small">Cancel</a>
        </div>
    </form>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>