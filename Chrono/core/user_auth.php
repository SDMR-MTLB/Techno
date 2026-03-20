<?php
// core/user_auth.php

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireUserLogin() {
    if (!isUserLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function getCurrentUser() {
    global $db; // need to have $db available, or pass connection
    if (!isUserLoggedIn()) return null;
    $stmt = $db->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}