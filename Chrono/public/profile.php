<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/user_auth.php';

$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$db = DB::getConnection();
$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
        if ($stmt->execute([$name, $phone, $address, $userId])) {
            $success = true;
            $user['name'] = $name;
            $user['phone'] = $phone;
            $user['address'] = $address;
            $_SESSION['user_name'] = $name;
        } else {
            $errors[] = "Update failed. Please try again.";
        }
    }
}

$pageTitle = 'My Profile';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-user"></i> My Profile
                </h2>
            </div>

            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <?php if ($success): ?>
                    <div style="background: #22c55e20; color: #22c55e; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #22c55e40;">
                        <i class="fas fa-check-circle"></i> Profile updated successfully.
                    </div>
                <?php endif; ?>

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
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Name *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required 
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Email (cannot be changed)</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled 
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-secondary); opacity: 0.7;">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Address</label>
                        <textarea name="address" rows="3" 
                                  style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 1rem; background: var(--bg-tertiary); color: var(--text-primary);"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">
                            Update Profile
                        </button>
                        <a href="<?= BASE_URL ?>/change-password.php" class="btn-primary" style="background: var(--bg-tertiary); color: var(--text-primary);">
                            Change Password
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>