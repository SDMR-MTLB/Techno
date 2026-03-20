<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/user_auth.php';

$db = DB::getConnection();
requireUserLogin(); // this function should already be defined
$isLoggedIn = true; // after requireUserLogin we know user is logged in

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Verify current password
    $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($current, $user['password_hash'])) {
        $errors[] = "Current password is incorrect.";
    }
    if (strlen($new) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    }
    if ($new !== $confirm) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors)) {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hash, $_SESSION['user_id']])) {
            $success = true;
        } else {
            $errors[] = "Update failed. Please try again.";
        }
    }
}

$pageTitle = 'Change Password';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-key"></i> Change Password
            </h2>
        </div>

        <div class="card" style="max-width: 500px; margin: 0 auto; background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <?php if ($success): ?>
                <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                    <i class="fas fa-check-circle"></i> Password changed successfully.
                </div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                    <ul style="margin-left: 1.5rem;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Current Password</label>
                    <input type="password" name="current_password" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">New Password</label>
                    <input type="password" name="new_password" required minlength="6" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Confirm New Password</label>
                    <input type="password" name="confirm_password" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                    Change Password
                </button>
                <p style="text-align: center; margin-top: 1.5rem;">
                    <a href="<?= BASE_URL ?>/profile.php" style="color: var(--brand-accent);">Back to Profile</a>
                </p>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>