<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/token.php';
require_once __DIR__ . '/../core/validator.php';

$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$token = $_GET['token'] ?? '';
if (!$token) {
    header('Location: ' . BASE_URL . '/forgot-password.php');
    exit;
}

// Validate token
$userId = validateUserToken($db, $token);
if (!$userId) {
    $error = "Invalid or expired token.";
}

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hash, $userId])) {
            clearUserToken($db, $userId);
            $success = true;
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}

$pageTitle = 'Reset Password';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 500px; margin: 0 auto;">
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">Reset Password</h2>

                <?php if (isset($error)): ?>
                    <div style="background: #ef444420; color: #ef4444; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #ef444440;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                    <p style="text-align: center;">
                        <a href="<?= BASE_URL ?>/forgot-password.php" style="color: var(--brand-accent);">Request new reset link</a>
                    </p>
                <?php elseif ($success): ?>
                    <div style="background: #22c55e20; color: #22c55e; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #22c55e40;">
                        <i class="fas fa-check-circle"></i> Your password has been reset successfully.
                    </div>
                    <p style="text-align: center;">
                        <a href="<?= BASE_URL ?>/login.php" class="btn-primary" style="display: inline-block; padding: 0.75rem 2rem;">Login now</a>
                    </p>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div style="background: #ef444420; color: #ef4444; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #ef444440;">
                            <ul style="margin-left: 1.2rem;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">New Password</label>
                            <input type="password" name="password" required minlength="6" 
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Confirm Password</label>
                            <input type="password" name="confirm_password" required 
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem;">
                            Reset Password
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>