<?php
// Start session and include necessary files
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/validator.php';

$error = '';

// If already logged in, redirect based on role
if (isset($_SESSION['admin_id'])) {
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super') {
        header('Location: ' . BASE_URL . '/admin/super/index.php');
    } else {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
    }
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'Both fields are required.';
    } else {
        // Fetch admin from database (include role column)
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT id, username, password_hash, role FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            session_regenerate_id(true);
            
            // Redirect based on role
            if ($admin['role'] === 'super') {
                header('Location: ' . BASE_URL . '/admin/super/index.php');
            } else {
                header('Location: ' . BASE_URL . '/admin/dashboard.php');
            }
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dark navy blue gradient - main theme navy */
        .bg-login {
            background: linear-gradient(135deg, #0B1F3A, #1A3B55);
        }
        /* Glassmorphism effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-login min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="glass-card rounded-2xl p-8 w-full max-w-md shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Admin Login</h2>
            <p class="text-white/70 text-sm mt-1">Partner & Super Admin access</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 bg-red-500/20 border border-red-500/50 rounded-lg p-3 text-red-200 text-sm text-center">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">
            <div>
                <label for="username" class="block text-white/80 text-sm font-medium mb-1">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-white/50"></i>
                    <input type="text" id="username" name="username" placeholder="Enter username" required
                           class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all">
                </div>
            </div>

            <div>
                <label for="password" class="block text-white/80 text-sm font-medium mb-1">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-white/50"></i>
                    <input type="password" id="password" name="password" placeholder="Enter password" required
                           class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-cyan-500 to-teal-500 text-white font-semibold rounded-xl hover:from-cyan-600 hover:to-teal-600 transform hover:scale-[1.02] transition-all duration-200 shadow-lg">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-white/50 text-xs">© <?= date('Y') ?> Partner Program. All rights reserved.</p>
        </div>
    </div>

</body>
</html>