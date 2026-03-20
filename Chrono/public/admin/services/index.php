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
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM services WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($statusFilter && in_array($statusFilter, ['active', 'inactive', 'deleted'])) {
    $sql .= " AND status = :status";
    $params[':status'] = $statusFilter;
}

$sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
$params[':limit'] = $limit;
$params[':offset'] = $offset;

$stmt = $db->prepare($sql);
foreach ($params as $key => &$val) {
    if ($key == ':limit' || $key == ':offset') {
        $stmt->bindValue($key, $val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val);
    }
}
$stmt->execute();
$services = $stmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM services WHERE 1";
$countParams = [];
if ($search) {
    $countSql .= " AND (name LIKE :search OR description LIKE :search)";
    $countParams[':search'] = "%$search%";
}
if ($statusFilter && in_array($statusFilter, ['active', 'inactive', 'deleted'])) {
    $countSql .= " AND status = :status";
    $countParams[':status'] = $statusFilter;
}
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalServices = $countStmt->fetchColumn();
$totalPages = ceil($totalServices / $limit);

$pageTitle = 'Manage Services';
$activePage = 'services';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Services</h2>
        <a href="<?= BASE_URL ?>/admin/services/add.php" class="admin-btn admin-btn-small">+ Add New Service</a>
    </div>

    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="active" <?= $statusFilter == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $statusFilter == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="deleted" <?= $statusFilter == 'deleted' ? 'selected' : '' ?>>Deleted</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-small">Filter</button>
    </form>

    <?php if (count($services) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Est. Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= $service['id'] ?></td>
                        <td><?= htmlspecialchars($service['name']) ?></td>
                        <td><?= htmlspecialchars(substr($service['description'] ?? '', 0, 50)) ?>...</td>
                        <td><?= $service['estimated_price'] ? '₱'.number_format($service['estimated_price'],2) : '-' ?></td>
                        <td><span class="status-badge status-<?= $service['status'] ?>"><?= ucfirst($service['status']) ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/services/edit.php?id=<?= $service['id'] ?>" class="admin-btn admin-btn-small">Edit</a>
                           <?php if ($service['status'] !== 'deleted'): ?>
                                <a href="<?= BASE_URL ?>/admin/services/delete.php?id=<?= $service['id'] ?>&token=<?= urlencode($csrfToken) ?>" class="admin-btn admin-btn-small admin-btn-danger" onclick="return confirm('Delete this service?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <!-- same pagination as products -->
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>No services found.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>