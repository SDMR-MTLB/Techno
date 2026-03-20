<?php
require_once __DIR__ . '/../../../../config/app.php';
require_once __DIR__ . '/../../../../core/session.php';
require_once __DIR__ . '/../../../../core/auth.php';
require_once __DIR__ . '/../../../../core/db.php';
require_once __DIR__ . '/../../../../core/csrf.php';

requireSuperAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/super/users/index.php');
    exit;
}

$token = $_GET['token'] ?? '';
if (!validateCsrfToken($token)) {
    die('Invalid CSRF token.');
}
clearCsrfToken();

$db = DB::getConnection();

// Optionally check if user has any bookings before deleting, or cascade.
// For simplicity, we'll allow deletion (foreign keys will handle).
$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header('Location: ' . BASE_URL . '/admin/super/users/index.php?deleted=1');
exit;