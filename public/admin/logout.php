<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../core/session.php';

// Destroy session
$_SESSION = [];
session_destroy();

// Redirect to login
header('Location: ' . BASE_URL . '/admin/login.php');
exit;