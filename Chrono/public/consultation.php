<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$db = DB::getConnection();
$isLoggedIn = isset($_SESSION['user_id']);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultation_type = $_POST['consultation_type'] ?? '';
    $business_type = trim($_POST['business_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $existing_setup = trim($_POST['existing_setup'] ?? '');
    $preferred_schedule = $_POST['preferred_schedule'] ?? '';

    if (!in_array($consultation_type, ['remote','onsite'])) $errors[] = "Invalid consultation type.";
    if (!$description) $errors[] = "Description is required.";

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO consultations (user_id, consultation_type, business_type, description, existing_setup, preferred_schedule) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$isLoggedIn ? $_SESSION['user_id'] : null, $consultation_type, $business_type, $description, $existing_setup, $preferred_schedule ?: null]);
        header('Location: ' . BASE_URL . '/consultation-confirm.php');
        exit;
    }
}

$pageTitle = 'Consultation';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-headset"></i> Request a Consultation
            </h2>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                <ul style="margin-left: 1.5rem;">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 600px; margin: 0 auto; background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow);">
            <form method="post">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Consultation Type *</label>
                    <select name="consultation_type" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                        <option value="">-- Select --</option>
                        <option value="remote">Remote</option>
                        <option value="onsite">Onsite</option>
                    </select>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Business Type</label>
                    <input type="text" name="business_type" value="<?= htmlspecialchars($business_type ?? '') ?>" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Description of Your Needs *</label>
                    <textarea name="description" required rows="4" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 1rem; background: var(--bg-tertiary); color: var(--text-primary);"><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Existing Setup (if any)</label>
                    <textarea name="existing_setup" rows="3" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 1rem; background: var(--bg-tertiary); color: var(--text-primary);"><?= htmlspecialchars($existing_setup ?? '') ?></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Preferred Schedule</label>
                    <input type="datetime-local" name="preferred_schedule" value="<?= htmlspecialchars($preferred_schedule ?? '') ?>" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                </div>
                <?php if (!$isLoggedIn): ?>
                    <p style="margin-bottom: 1rem; color: var(--text-secondary);">
                        You are submitting as a guest. <a href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" style="color: var(--brand-accent);">Login</a> to track your request.
                    </p>
                <?php endif; ?>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                    Submit Consultation
                </button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>