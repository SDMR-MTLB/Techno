<?php
// core/auth.php

require_once __DIR__ . '/session.php';

// Existing functions (keep them)
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

// New role-based functions
function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super';
}

function requireSuperAdmin() {
    requireLogin(); // First ensure logged in
    if (!isSuperAdmin()) {
        // Not a super admin – redirect to their own dashboard (partner)
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    }
}

// Optional: helper to get current admin's role
function getCurrentAdminRole() {
    return $_SESSION['admin_role'] ?? null;
}