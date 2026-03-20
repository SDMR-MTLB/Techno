<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';
require_once __DIR__ . '/../../../../core/csrf.php';

requireSuperAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/super/services/index.php');
    exit;
}

$token = $_GET['token'] ?? '';
if (!validateCsrfToken($token)) {
    die('Invalid CSRF token.');
}
clearCsrfToken();

$db = DB::getConnection();

// Soft delete: set status to 'deleted' – no admin_id condition
$stmt = $db->prepare("UPDATE services SET status = 'deleted' WHERE id = ?");
$stmt->execute([$id]);

header('Location: ' . BASE_URL . '/admin/super/services/index.php?deleted=1');
exit;