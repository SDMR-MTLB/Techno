<?php
// core/validator.php

/**
 * Sanitize input for safe output (prevents XSS)
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if a required field is empty
 */
function validateRequired($field, $name) {
    if (empty(trim($field))) {
        return "$name is required.";
    }
    return null;
}

/**
 * Validate phone number format (simple)
 */
function validatePhone($phone) {
    // Allow digits, spaces, plus, hyphen, parentheses – adjust as needed
    if (!preg_match('/^[0-9\s\+\-\(\)]{7,20}$/', $phone)) {
        return "Invalid phone number format.";
    }
    return null;
}

/**
 * Validate email
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email address.";
    }
    return null;
}