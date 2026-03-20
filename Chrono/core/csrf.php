<?php
// core/csrf.php

require_once __DIR__ . '/session.php';

/**
 * Generate a CSRF token and store it in session.
 *
 * @return string
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token.
 *
 * @param string $token
 * @return bool
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Unset CSRF token after use (optional, but recommended).
 */
function clearCsrfToken() {
    unset($_SESSION['csrf_token']);
}