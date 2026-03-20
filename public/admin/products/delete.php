<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/products/index.php');
    exit;
}

$token = $_GET['token'] ?? '';
if (!validateCsrfToken($token)) {
    die('Invalid CSRF token.');
}
clearCsrfToken();

$db = DB::getConnection();
$adminId = $_SESSION['admin_id'];
$isSuper = ($_SESSION['admin_role'] ?? '') === 'super';

// Optional ownership check (but we'll just update based on ID and let FK handle if needed)
// To be safe, we can verify ownership (unless super)
if (!$isSuper) {
    $check = $db->prepare("SELECT id FROM products WHERE id = ? AND admin_id = ?");
    $check->execute([$id, $adminId]);
    if (!$check->fetch()) {
        header('Location: ' . BASE_URL . '/admin/products/index.php');
        exit;
    }
}

$stmt = $db->prepare("UPDATE products SET status = 'deleted' WHERE id = ?");
$stmt->execute([$id]);

header('Location: ' . BASE_URL . '/admin/products/index.php?deleted=1');
exit;