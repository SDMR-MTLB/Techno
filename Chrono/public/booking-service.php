<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$db = DB::getConnection();

$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
if (!$service_id) {
    header('Location: services.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM services WHERE id = ? AND status = 'active'");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: services.php');
    exit;
}

// Fetch active offices
$officesStmt = $db->query("SELECT * FROM offices WHERE is_active = 1");
$offices = $officesStmt->fetchAll();

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $office_id = (int)($_POST['office_id'] ?? 0);
    $client_address = trim($_POST['client_address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $preferred_date = $_POST['preferred_date'] ?? '';
    $preferred_time = $_POST['preferred_time'] ?? '';

    if (!$office_id) $errors[] = "Please select an office.";
    if (!$client_address) $errors[] = "Client address is required.";
    if (!$description) $errors[] = "Description of issue is required.";

    if (empty($errors)) {
        // Generate booking code
        $code = 'BOK-' . date('Ymd') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $db->prepare("INSERT INTO bookings (booking_code, user_id, office_id, booking_type, service_id, client_address, description, preferred_date, preferred_time, status) VALUES (?, ?, ?, 'service', ?, ?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$code, $_SESSION['user_id'], $office_id, $service_id, $client_address, $description, $preferred_date ?: null, $preferred_time ?: null])) {
            header('Location: ' . BASE_URL . '/booking-confirm.php?code=' . $code);
            exit;
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}

$pageTitle = 'Book Service';
include __DIR__ . '/includes/main-header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-calendar-plus"></i> Book Service: <?= htmlspecialchars($service['name']) ?>
            </h2>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                <ul style="margin-left: 1.5rem;">
                    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card" style="background: var(--card-bg); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow); max-width: 600px; margin: 0 auto;">
            <form method="post">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Select Office *</label>
                    <select name="office_id" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                        <option value="">-- Choose an office --</option>
                        <?php foreach ($offices as $office): ?>
                            <option value="<?= $office['id'] ?>"><?= htmlspecialchars($office['name']) ?> (<?= htmlspecialchars($office['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Your Address *</label>
                    <input type="text" name="client_address" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Description of Issue *</label>
                    <textarea name="description" required rows="4" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 1rem; background: var(--bg-tertiary); color: var(--text-primary);"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Preferred Date</label>
                        <input type="date" name="preferred_date" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Preferred Time</label>
                        <input type="time" name="preferred_time" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 9999px; background: var(--bg-tertiary); color: var(--text-primary);">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                    Submit Booking
                </button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>