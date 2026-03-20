<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/user_auth.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Both fields are required.';
    } else {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            session_regenerate_id(true);
            // Redirect to intended page or dashboard
            $redirect = $_SESSION['redirect_after_login'] ?? BASE_URL . '/dashboard.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$isLoggedIn = false; // for header
$pageTitle = 'Login';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 400px; margin: 0 auto;">
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">Login to Your Account</h2>

                <?php if ($error): ?>
                    <div style="background: #ef444420; color: #ef4444; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #ef444440;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Email</label>
                        <input type="email" name="email" required 
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Password</label>
                        <input type="password" name="password" required 
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem; display: flex; align-items: center; justify-content: center;">
                        Login
                    </button>
                </form>

                <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
                    Don't have an account? <a href="<?= BASE_URL ?>/register.php" style="color: var(--brand-accent);">Register</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>