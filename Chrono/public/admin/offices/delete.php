<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/admin/offices/index.php');
    exit;
}

$token = $_GET['token'] ?? '';
if (!validateCsrfToken($token)) {
    die('Invalid CSRF token.');
}
clearCsrfToken();

$activate = isset($_GET['activate']) ? 1 : 0;
$newStatus = $activate ? 1 : 0;

$db = DB::getConnection();
$stmt = $db->prepare("UPDATE offices SET is_active = ? WHERE id = ?");
$stmt->execute([$newStatus, $id]);

header('Location: ' . BASE_URL . '/admin/offices/index.php?updated=1');
exit;