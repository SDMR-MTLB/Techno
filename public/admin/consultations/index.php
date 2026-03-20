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
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT c.*, u.name as user_name, u.email 
        FROM consultations c 
        LEFT JOIN users u ON c.user_id = u.id 
        WHERE 1";
$params = [];

if ($statusFilter) {
    $sql .= " AND c.status = :status";
    $params[':status'] = $statusFilter;
}
if ($search) {
    $sql .= " AND (c.description LIKE :search OR u.name LIKE :search OR u.email LIKE :search)";
    $params[':search'] = "%$search%";
}

$countSql = "SELECT COUNT(*) FROM consultations c LEFT JOIN users u ON c.user_id = u.id WHERE 1";
$countParams = $params;
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

$sql .= " ORDER BY c.id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$consultations = $stmt->fetchAll();

$pageTitle = 'Consultations';
$activePage = 'consultations';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>Consultation Requests</h2>

    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="reviewed" <?= $statusFilter == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
            <option value="converted" <?= $statusFilter == 'converted' ? 'selected' : '' ?>>Converted</option>
            <option value="rejected" <?= $statusFilter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-small">Filter</button>
    </form>

    <?php if (count($consultations) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultations as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['user_name'] ?? 'Guest') ?><br><small><?= htmlspecialchars($c['email'] ?? '') ?></small></td>
                        <td><?= ucfirst($c['consultation_type']) ?></td>
                        <td><?= htmlspecialchars(substr($c['description'], 0, 50)) ?>...</td>
                        <td><?= $c['preferred_schedule'] ? date('Y-m-d H:i', strtotime($c['preferred_schedule'])) : '-' ?></td>
                        <td><span class="status-badge status-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($c['created_at'])) ?></td>
                        <td><a href="view.php?id=<?= $c['id'] ?>" class="admin-btn admin-btn-small">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination"><!-- links similar to other modules --></div>
        <?php endif; ?>
    <?php else: ?>
        <p>No consultations found.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>