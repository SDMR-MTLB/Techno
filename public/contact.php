<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/validator.php';

$db = DB::getConnection();
$isLoggedIn = isset($_SESSION['user_id']); // needed for header
$success = false;
$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $formData = compact('name', 'email', 'subject', 'message');

    $err = validateRequired($name, 'Name');
    if ($err) $errors[] = $err;

    $err = validateEmail($email);
    if ($err) $errors[] = $err;

    $err = validateRequired($message, 'Message');
    if ($err) $errors[] = $err;

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject ?: null, $message])) {
            $success = true;
            $formData = [];
        } else {
            $errors[] = "Failed to send message. Please try again later.";
        }
    }
}

$pageTitle = 'Contact Us';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-envelope"></i> Contact Us
            </h2>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Left column: Contact info -->
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Get in Touch</h3>
                <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">Have questions? We're here to help. Fill out the form or reach us directly.</p>
                <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                    <i class="fas fa-map-marker-alt" style="color: var(--brand-accent); width: 2rem;"></i>
                    <span>123 Vendo St., Makati City, Philippines</span>
                </div>
                <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                    <i class="fas fa-phone" style="color: var(--brand-accent); width: 2rem;"></i>
                    <span>+63 (2) 1234 5678</span>
                </div>
                <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                    <i class="fas fa-envelope" style="color: var(--brand-accent); width: 2rem;"></i>
                    <span>support@nethub.ph</span>
                </div>
                <div style="margin-top: 2rem;">
                    <h4 style="color: var(--text-primary);">Office Hours</h4>
                    <p style="color: var(--text-secondary);">Monday – Friday: 9:00 AM – 6:00 PM<br>Saturday: 10:00 AM – 4:00 PM<br>Sunday: Closed</p>
                </div>
            </div>

            <!-- Right column: Contact form -->
            <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
                <?php if ($success): ?>
                    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                        <i class="fas fa-check-circle"></i> Your message has been sent successfully! We'll get back to you soon.
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                        <ul style="margin-left: 1.5rem;">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Full Name *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required style="width:100%; padding:0.75rem 1rem; border:1px solid var(--border-color); border-radius:9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Email Address *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required style="width:100%; padding:0.75rem 1rem; border:1px solid var(--border-color); border-radius:9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Subject (optional)</label>
                        <input type="text" name="subject" value="<?= htmlspecialchars($formData['subject'] ?? '') ?>" style="width:100%; padding:0.75rem 1rem; border:1px solid var(--border-color); border-radius:9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Message *</label>
                        <textarea name="message" required rows="6" style="width:100%; padding:0.75rem 1rem; border:1px solid var(--border-color); border-radius:1rem; background: var(--bg-tertiary); color: var(--text-primary);"><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>