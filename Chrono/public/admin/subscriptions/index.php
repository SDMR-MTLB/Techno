<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT s.*, u.name as user_name, u.email 
        FROM subscriptions s
        JOIN users u ON s.user_id = u.id
        WHERE 1";
$params = [];

if ($statusFilter) {
    $sql .= " AND s.status = :status";
    $params[':status'] = $statusFilter;
}
if ($search) {
    $sql .= " AND (u.name LIKE :search OR u.email LIKE :search)";
    $params[':search'] = "%$search%";
}

$countSql = "SELECT COUNT(*) FROM subscriptions s JOIN users u ON s.user_id = u.id WHERE 1";
$countParams = $params;
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

$sql .= " ORDER BY s.id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$subscriptions = $stmt->fetchAll();

$pageTitle = 'Manage Subscriptions';
$activePage = 'subscriptions';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <h2>Subscriptions</h2>

    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search by user..." value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="active" <?= $statusFilter == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="paused" <?= $statusFilter == 'paused' ? 'selected' : '' ?>>Paused</option>
            <option value="cancelled" <?= $statusFilter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-small">Filter</button>
    </form>

    <?php if (count($subscriptions) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Start Date</th>
                    <th>Next Billing</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscriptions as $sub): ?>
                    <tr>
                        <td><?= $sub['id'] ?></td>
                        <td><?= htmlspecialchars($sub['user_name']) ?><br><small><?= htmlspecialchars($sub['email']) ?></small></td>
                        <td><?= ucfirst($sub['plan']) ?></td>
                        <td><?= $sub['start_date'] ?></td>
                        <td><?= $sub['next_billing'] ?></td>
                        <td><span class="status-badge status-<?= $sub['status'] ?>"><?= ucfirst($sub['status']) ?></span></td>
                        <td>
                            <a href="view.php?id=<?= $sub['id'] ?>" class="admin-btn admin-btn-small">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination"><!-- links --></div>
        <?php endif; ?>
    <?php else: ?>
        <p>No subscriptions found.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>