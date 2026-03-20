<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';

requireLogin();

$db = DB::getConnection();

// Pagination and filtering
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$typeFilter = $_GET['type'] ?? 'all'; // 'all', 'product', 'service'
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Base query
$sql = "SELECT r.*, 
               (SELECT COUNT(*) FROM request_items ri WHERE ri.request_id = r.id) as item_count 
        FROM requests r 
        WHERE 1";
$params = [];

if ($typeFilter !== 'all') {
    $sql .= " AND r.request_type = :type";
    $params[':type'] = $typeFilter;
}

if ($statusFilter) {
    $sql .= " AND r.status = :status";
    $params[':status'] = $statusFilter;
}

if ($search) {
    $sql .= " AND (r.customer_name LIKE :search OR r.customer_contact LIKE :search OR r.request_code LIKE :search)";
    $params[':search'] = "%$search%";
}

// Count total
$countSql = preg_replace('/SELECT r.*,.*?FROM/', 'SELECT COUNT(*) FROM', $sql);
$countStmt = $db->prepare($countSql);
foreach ($params as $key => $val) {
    $countStmt->bindValue($key, $val);
}
$countStmt->execute();
$totalRequests = $countStmt->fetchColumn();
$totalPages = ceil($totalRequests / $limit);

// Fetch with pagination
$sql .= " ORDER BY r.id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $val) {
    if ($key === ':limit' || $key === ':offset') continue;
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$requests = $stmt->fetchAll();

$pageTitle = 'Manage Requests';
$activePage = 'requests';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>All Requests</h2>
        <div>
            <a href="?type=all" class="admin-btn admin-btn-small <?= $typeFilter === 'all' ? 'active' : '' ?>">All</a>
            <a href="?type=product" class="admin-btn admin-btn-small <?= $typeFilter === 'product' ? 'active' : '' ?>">Product</a>
            <a href="?type=service" class="admin-btn admin-btn-small <?= $typeFilter === 'service' ? 'active' : '' ?>">Service</a>
        </div>
    </div>

    <!-- Filter & Search -->
    <form method="get" class="filter-form">
        <input type="hidden" name="type" value="<?= htmlspecialchars($typeFilter) ?>">
        <input type="text" name="search" placeholder="Search by name, contact, or request ID" value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $statusFilter == 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="completed" <?= $statusFilter == 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= $statusFilter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-small">Filter</button>
    </form>

    <?php if (count($requests) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Type</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= htmlspecialchars($req['request_code']) ?></td>
                        <td><?= htmlspecialchars($req['customer_name']) ?></td>
                        <td><?= htmlspecialchars($req['customer_contact']) ?></td>
                        <td><?= ucfirst($req['request_type']) ?></td>
                        <td><?= $req['item_count'] ?></td>
                        <td><span class="status-badge status-<?= $req['status'] ?>"><?= ucfirst($req['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($req['created_at'])) ?></td>
                        <td>
                            <a href="view.php?id=<?= $req['id'] ?>" class="admin-btn admin-btn-small">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&type=<?= $typeFilter ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&type=<?= $typeFilter ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&type=<?= $typeFilter ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>No requests found.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>