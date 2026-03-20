<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/validator.php';
require_once __DIR__ . '/../../core/csrf.php';

requireLogin(); // both partner and super must be logged in

$db = DB::getConnection();
$adminId = $_SESSION['admin_id'];
$csrfToken = generateCsrfToken();

// Fetch current admin data
$stmt = $db->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if (!$admin) {
    // Should not happen, but just in case
    header('Location: ' . BASE_URL . '/admin/logout.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    clearCsrfToken();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validate username
    $err = validateRequired($username, 'Username');
    if ($err) $errors[] = $err;

    // If username changed, check uniqueness
    if ($username !== $admin['username']) {
        $check = $db->prepare("SELECT id FROM admins WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $errors[] = 'Username already taken.';
        }
    }

    // If password provided, validate
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }
    }

    if (empty($errors)) {
        // Build update query
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $updateStmt = $db->prepare("UPDATE admins SET username = ?, password_hash = ? WHERE id = ?");
            $updateStmt->execute([$username, $hash, $adminId]);
        } else {
            $updateStmt = $db->prepare("UPDATE admins SET username = ? WHERE id = ?");
            $updateStmt->execute([$username, $adminId]);
        }

        // Update session username if changed
        $_SESSION['admin_username'] = $username;
        $success = true;
        // Refresh admin data
        $admin['username'] = $username;
    }
}

$pageTitle = 'Profile';
$activePage = 'profile';

include __DIR__ . '/partials/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Your Profile</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Update your username and password.</p>
    </div>

    <?php if ($success): ?>
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-green-600 dark:text-green-400">
            Profile updated successfully.
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username *</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? $admin['username']) ?>" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all-200">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>