<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../core/session.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/db.php';

requireSuperAdmin();

$db = DB::getConnection();

// Count partner admins
$stmt = $db->query("SELECT COUNT(*) FROM admins WHERE role = 'partner'");
$totalPartners = $stmt->fetchColumn();

// Count unread messages
$stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$unreadMessages = $stmt->fetchColumn();

// Count total users (optional for stats)
$stmt = $db->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

// Count pending bookings
$stmt = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$pendingBookings = $stmt->fetchColumn();

$pageTitle = 'Super Dashboard';
$activePage = 'super_dashboard';

include __DIR__ . '/../partials/header.php';
?>

<!-- Page Header -->
<div class="mb-6 sm:mb-8">
    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Super Admin Dashboard</h1>
    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Complete control over the platform.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <!-- Total Partners -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Partner Stores</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $totalPartners ?></p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-2 flex items-center gap-1">
                    <i class="fas fa-store"></i> Active
                </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Unread Messages -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Unread Messages</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $unreadMessages ?></p>
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2 flex items-center gap-1">
                    <i class="fas fa-envelope"></i> Awaiting reply
                </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-message text-purple-600 dark:text-purple-400 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Registered Users</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $totalUsers ?></p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 flex items-center gap-1">
                    <i class="fas fa-user"></i> Total accounts
                </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-circle text-indigo-600 dark:text-indigo-400 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Pending Bookings</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $pendingBookings ?></p>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-2 flex items-center gap-1">
                    <i class="fas fa-clock"></i> Awaiting approval
                </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-orange-600 dark:text-orange-400 text-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mb-8">
    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <a href="<?= BASE_URL ?>/admin/super/admins.php" class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-200">
                    <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Manage Partner Admins</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Add, edit, or remove partners</p>
                </div>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/super/messages.php" class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors duration-200">
                    <i class="fas fa-message text-green-600 dark:text-green-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">View Messages</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?= $unreadMessages ?> unread</p>
                </div>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/super/offices/index.php" class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors duration-200">
                    <i class="fas fa-building text-purple-600 dark:text-purple-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Offices</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage service locations</p>
                </div>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/super/users/index.php" class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center group-hover:bg-indigo-600 transition-colors duration-200">
                    <i class="fas fa-users text-indigo-600 dark:text-indigo-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Users</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage customer accounts</p>
                </div>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/super/bookings/index.php" class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-orange-500 dark:hover:border-orange-500 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center group-hover:bg-orange-600 transition-colors duration-200">
                    <i class="fas fa-calendar-check text-orange-600 dark:text-orange-400 group-hover:text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Bookings</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Approve or reject bookings</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>