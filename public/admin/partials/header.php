<?php
// admin/partials/header.php
// Expects: $pageTitle (string), $activePage (string) - e.g., 'dashboard', 'products', 'super_dashboard', etc.
// Also uses $_SESSION['admin_role'] to determine which navigation to show.

if (!isset($pageTitle)) $pageTitle = 'Admin Panel';

// Determine role (default to partner if not set, but should be set at login)
$role = $_SESSION['admin_role'] ?? 'partner';
$username = $_SESSION['admin_username'] ?? 'Admin';

// Set branding based on role
$brandName = ($role === 'super') ? 'SuperAdmin' : 'PartnerPanel';
$brandIcon = ($role === 'super') ? 'crown' : 'handshake';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= $brandName ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
        
        /* Smooth transitions */
        .transition-all-200 { transition: all 0.2s ease; }
        
        /* Sidebar active indicator */
        .nav-item.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, transparent 100%);
            border-left: 3px solid #2563eb;
        }
        
        /* Card hover effect */
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        
        /* Table row hover */
        .table-row-hover:hover { background-color: rgba(59, 130, 246, 0.05); }
        .dark .table-row-hover:hover { background-color: rgba(59, 130, 246, 0.1); }
        
        /* Status badges */
        .badge { @apply px-2.5 py-1 text-xs font-medium rounded-full; }
        
        /* Animation for stats */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-animate { animation: countUp 0.5s ease-out; }

        /* === Responsive sidebar & overlay === */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
        #sidebarOverlay {
            transition: opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }
        body.sidebar-open #sidebarOverlay {
            opacity: 1;
            pointer-events: auto;
        }
        body.sidebar-open #sidebar {
            transform: translateX(0) !important;
        }
        @media (min-width: 1024px) {
            #sidebar {
                transform: translateX(0) !important;
            }
            #sidebarOverlay {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <!-- Mobile overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-r border-gray-200 dark:border-gray-700 z-40 transform -translate-x-full lg:translate-x-0 shadow-xl lg:shadow-none">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-<?= $brandIcon ?> text-white text-sm"></i>
                </div>
                <span class="font-semibold text-lg"><?= $brandName ?></span>
            </div>
        </div>
        
        <!-- Navigation (Role‑Based) -->
        <nav class="p-4 space-y-1 overflow-y-auto max-h-[calc(100vh-4rem)]">
            <?php if ($role === 'super'): ?>
                <!-- Super Admin Navigation -->
                <a href="<?= BASE_URL ?>/admin/super/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'super_dashboard' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-chart-pie w-5"></i>
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/super/services/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'super_services' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-cogs w-5"></i>
                    Services
                </a>
                <a href="<?= BASE_URL ?>/admin/super/packages/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'super_packages' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-boxes w-5"></i>
                    Packages
                </a>
                <a href="<?= BASE_URL ?>/admin/super/admins.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'admins' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-users-cog w-5"></i>
                    Manage Partners
                </a>
                <a href="<?= BASE_URL ?>/admin/super/messages.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'messages' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-message w-5"></i>
                    Messages
                </a>
                <!-- New Modules -->
                <a href="<?= BASE_URL ?>/admin/super/offices/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'super_offices' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-building w-5"></i>
                    Offices
                </a>
                <a href="<?= BASE_URL ?>/admin/super/users/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'super_users' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-users w-5"></i>
                    Users
                </a>
                <a href="<?= BASE_URL ?>/admin/super/bookings/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'super_bookings' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-calendar-check w-5"></i>
                    Bookings
                </a>
            <?php else: ?>
                <!-- Partner Admin Navigation -->
                <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'dashboard' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-chart-pie w-5"></i>
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/products/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'products' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-box w-5"></i>
                    My Products
                </a>
                <a href="<?= BASE_URL ?>/admin/packages/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'packages' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-cubes w-5"></i>
                    Packages
                </a>
                <a href="<?= BASE_URL ?>/admin/reports/index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'reports' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-chart-line w-5"></i>
                    Reports
                </a>
            <?php endif; ?>
            
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Settings</p>
                <a href="<?= BASE_URL ?>/admin/profile.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all-200 <?= $activePage === 'profile' ? 'active text-primary-600 dark:text-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' ?>">
                    <i class="fas fa-user-cog w-5"></i>
                    Profile
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="min-h-screen flex flex-col lg:ml-64 transition-margin duration-300">
        
        <!-- Top Navigation -->
        <header class="h-16 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
            <div class="h-full px-4 sm:px-6 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <!-- Mobile menu button -->
                    <button id="mobileMenuBtn" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 lg:hidden transition-all-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Search (optional) – shown only for partners -->
                    <?php if ($role !== 'super'): ?>
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Search products, orders..." 
                                   class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all-200">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                    <!-- Theme Toggle -->
                    <button id="themeToggle" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-all-200">
                        <i class="fas fa-sun hidden dark:block"></i>
                        <i class="fas fa-moon block dark:hidden"></i>
                    </button>
                    
                    <!-- Notifications (optional) -->
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-all-200">
                        <i class="fas fa-bell"></i>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center gap-1 sm:gap-3 p-1.5 sm:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-all-200">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            </div>
                            <span class="text-sm font-medium hidden sm:block"><?= htmlspecialchars($username) ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:inline"></i>
                        </button>
                        
                        <!-- Dropdown -->
                        <div id="userMenu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                            <a href="<?= BASE_URL ?>/admin/profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <?php if ($role === 'super'): ?>
                                <!-- Super admin might have additional settings -->
                            <?php endif; ?>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <a href="<?= BASE_URL ?>/admin/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Main Content (opening tag) – will be closed in footer -->
        <main class="flex-1 p-4 sm:p-6">