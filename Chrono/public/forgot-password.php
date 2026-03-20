<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/token.php';
require_once __DIR__ . '/../core/mail.php';
require_once __DIR__ . '/../core/validator.php';

$db = DB::getConnection();
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $err = validateEmail($email) ?: null;
    if ($err) {
        $errors[] = $err;
    } else {
        // Check if user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            // Generate token
            $token = generateToken();
            storeUserToken($db, $user['id'], $token, 24);
            // Send email
            $resetLink = BASE_URL . '/reset-password.php?token=' . $token;
            $subject = "Password Reset Request";
            $body = "<h2>Password Reset</h2>
                     <p>Click the link below to reset your password:</p>
                     <p><a href='$resetLink'>$resetLink</a></p>
                     <p>This link expires in 24 hours.</p>";
            sendEmail($email, $subject, $body);
        }
        // Always show success to prevent email enumeration
        $success = true;
    }
}

$pageTitle = 'Forgot Password';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 500px; margin: 0 auto;">
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">Reset Password</h2>

                <?php if ($success): ?>
                    <div style="background: #22c55e20; color: #22c55e; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #22c55e40;">
                        <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                        If that email is registered, we've sent a password reset link.
                    </div>
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

                    <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>

                    <form method="post">
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Email Address</label>
                            <input type="email" name="email" required 
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem;">
                            Send Reset Link
                        </button>
                    </form>
                <?php endif; ?>

                <p style="text-align: center; margin-top: 1.5rem;">
                    <a href="<?= BASE_URL ?>/login.php" style="color: var(--brand-accent);">Back to Login</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>