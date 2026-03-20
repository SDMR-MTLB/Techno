<?php
// config/app.php

// Timezone
date_default_timezone_set('Asia/Manila'); // Change as needed

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Base URL (for generating links)
define('BASE_URL', 'http://localhost/Chrono/public');

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
define('SMTP_FROM', 'noreply@pisowifivendo.com');
define('SMTP_FROM_NAME', 'Pisowifi Vendo');