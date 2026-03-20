<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/csrf.php';

requireLogin();

$db = DB::getConnection();
$csrfToken = generateCsrfToken();

// Pagination and filtering
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? ''; // '1' for active, '0' for inactive

$sql = "SELECT * FROM offices WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (name LIKE :search OR address LIKE :search OR city LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($statusFilter !== '') {
    $sql .= " AND is_active = :status";
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
$offices = $stmt->fetchAll();

// Count total
$countSql = "SELECT COUNT(*) FROM offices WHERE 1";
$countParams = [];
if ($search) {
    $countSql .= " AND (name LIKE :search OR address LIKE :search OR city LIKE :search)";
    $countParams[':search'] = "%$search%";
}
if ($statusFilter !== '') {
    $countSql .= " AND is_active = :status";
    $countParams[':status'] = $statusFilter;
}
$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalOffices = $countStmt->fetchColumn();
$totalPages = ceil($totalOffices / $limit);

$pageTitle = 'Manage Offices';
$activePage = 'offices';

include __DIR__ . '/../partials/header.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Offices</h2>
        <a href="<?= BASE_URL ?>/admin/offices/add.php" class="admin-btn admin-btn-small">+ Add New Office</a>
    </div>

    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search offices..." value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All</option>
            <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Inactive</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-small">Filter</button>
    </form>

    <?php if (count($offices) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($offices as $office): ?>
                    <tr>
                        <td><?= $office['id'] ?></td>
                        <td><?= htmlspecialchars($office['name']) ?></td>
                        <td><?= htmlspecialchars($office['address']) ?></td>
                        <td><?= htmlspecialchars($office['city']) ?></td>
                        <td><?= htmlspecialchars($office['contact'] ?? '-') ?></td>
                        <td>
                            <span class="status-badge <?= $office['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                <?= $office['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/offices/edit.php?id=<?= $office['id'] ?>" class="admin-btn admin-btn-small">Edit</a>
                            <?php if ($office['is_active']): ?>
                                <a href="<?= BASE_URL ?>/admin/offices/delete.php?id=<?= $office['id'] ?>&token=<?= urlencode($csrfToken) ?>" class="admin-btn admin-btn-small admin-btn-danger" onclick="return confirm('Deactivate this office?')">Deactivate</a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/admin/offices/delete.php?id=<?= $office['id'] ?>&token=<?= urlencode($csrfToken) ?>&activate=1" class="admin-btn admin-btn-small" onclick="return confirm('Activate this office?')">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>No offices found.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>