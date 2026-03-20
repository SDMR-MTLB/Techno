<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/packages/index.php');
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

// Verify ownership (unless super)
if (!$isSuper) {
    $stmt = $db->prepare("SELECT id FROM packages WHERE id = ? AND admin_id = ?");
    $stmt->execute([$id, $adminId]);
    if (!$stmt->fetch()) {
        header('Location: ' . BASE_URL . '/admin/packages/index.php');
        exit;
    }
}

$activate = isset($_GET['activate']) ? 1 : 0;
$newStatus = $activate ? 'active' : 'inactive';

$stmt = $db->prepare("UPDATE packages SET status = ? WHERE id = ?");
$stmt->execute([$newStatus, $id]);

header('Location: ' . BASE_URL . '/admin/packages/index.php?updated=1');
exit;