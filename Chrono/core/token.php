<?php
// core/token.php
// Secure token generation and validation

/**
 * Generate a random token.
 * @param int $length Length of token in bytes (will be hex encoded, so double length)
 * @return string Hex token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Store a token in the database for a user.
 * @param PDO $db Database connection
 * @param int $userId User ID
 * @param string $token The token
 * @param int $expiryHours Hours until token expires (default 24)
 * @return bool Success
 */
function storeUserToken($db, $userId, $token, $expiryHours = 24) {
    $expires = date('Y-m-d H:i:s', strtotime("+$expiryHours hours"));
    $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    return $stmt->execute([$token, $expires, $userId]);
}

/**
 * Validate a token for a user.
 * @param PDO $db Database connection
 * @param string $token The token to validate
 * @return int|false User ID if valid, false otherwise
 */
function validateUserToken($db, $token) {
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['id'] : false;
}

/**
 * Clear a user's token after use.
 * @param PDO $db Database connection
 * @param int $userId
 * @return bool
 */
function clearUserToken($db, $userId) {
    $stmt = $db->prepare("UPDATE users SET reset_token = NULL, reset_expires = NULL WHERE id = ?");
    return $stmt->execute([$userId]);
}