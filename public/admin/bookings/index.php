<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';

requireLogin();

$db = DB::getConnection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT b.*, u.name as customer_name, o.name as office_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN offices o ON b.office_id = o.id
        WHERE 1";
$params = [];

if ($typeFilter) {
    $sql .= " AND b.booking_type = :type";
    $params[':type'] = $typeFilter;
}
if ($statusFilter) {
    $sql .= " AND b.status = :status";
    $params[':status'] = $statusFilter;
}
if ($search) {
    $sql .= " AND (b.booking_code LIKE :search OR u.name LIKE :search)";
    $params[':search'] = "%$search%";
}

$countSql = "SELECT COUNT(*) FROM bookings b JOIN users u ON b.user_id = u.id WHERE 1";
$countParams = $params;
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

$sql .= " ORDER BY b.id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll();

$pageTitle = 'Manage Bookings';
$activePage = 'bookings';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>All Bookings</h2>

    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search by code or customer..." value="<?= htmlspecialchars($search) ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="service" <?= $typeFilter == 'service' ? 'selected' : '' ?>>Service</option>
            <option value="package" <?= $typeFilter == 'package' ? 'selected' : '' ?>>Package</option>
            <option value="consultation" <?= $typeFilter == 'consultation' ? 'selected' : '' ?>>Consultation</option>
        </select>
        <select name="status">
            <option value="">All Status</option>
            <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $statusFilter == 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $statusFilter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
            <option value="completed" <?= $statusFilter == 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-small">Filter</button>
    </form>

    <?php if (count($bookings) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Office</th>
                    <th>Preferred Date</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['booking_code']) ?></td>
                        <td><?= htmlspecialchars($b['customer_name']) ?></td>
                        <td><?= ucfirst($b['booking_type']) ?></td>
                        <td><?= htmlspecialchars($b['office_name']) ?></td>
                        <td><?= $b['preferred_date'] ? date('Y-m-d', strtotime($b['preferred_date'])) : '-' ?></td>
                        <td><span class="status-badge status-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($b['created_at'])) ?></td>
                        <td><a href="view.php?id=<?= $b['id'] ?>" class="admin-btn admin-btn-small">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination"><!-- links similar --></div>
        <?php endif; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>