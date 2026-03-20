<?php
// public/includes/main-header.php
// Expects $pageTitle (string) and $isLoggedIn (bool) to be set before inclusion.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - NetHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
    (function() {
        const savedTheme = localStorage.getItem('theme');
        const html = document.documentElement;
        if (savedTheme === 'dark') {
            html.classList.add('dark');
        } else if (savedTheme === 'light') {
            html.classList.remove('dark');
        } else {
            // No saved preference – use OS preference
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
    })();
</script>
    <style>
        /* CSS Variables for Theming */
        :root {
            --bg-primary: #f1f5f9;
            --bg-secondary: #ffffff;
            --bg-tertiary: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --brand-primary: #1e3a5f;
            --brand-accent: #0ea5e9;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 25px -5px rgba(30, 41, 59, 0.1), 0 10px 10px -5px rgba(30, 41, 59, 0.04);
            --glow: none;
        }

        .dark {
            --bg-primary: #000000;
            --bg-secondary: #0a0a0a;
            --bg-tertiary: #141414;
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            --brand-primary: #007ac3;
			--brand-accent: #0197f6;    
            --border-color: #27272a;
            --card-bg: #0a0a0a;
            --shadow: 0 0 20px rgba(29, 91, 148, 0.1);
            --shadow-lg: 0 0 30px rgba(29, 91, 148, 0.15);
            --glow: 0 0 20px rgba(29, 91, 148, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.5s ease, color 0.5s ease;
        }
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        @media (min-width: 640px) { .container { padding: 0 1.5rem; } }
        @media (min-width: 1024px) { .container { padding: 0 2rem; } }

        /* Theme Toggle */
        .theme-toggle {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: var(--bg-tertiary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .theme-toggle:hover { background: var(--brand-accent); }
        .theme-toggle i {
            font-size: 1.2rem;
            color: var(--text-primary);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .theme-toggle .sun-icon {
            position: absolute;
            opacity: 0;
            transform: rotate(90deg) scale(0);
        }
        .theme-toggle .moon-icon {
            position: absolute;
            opacity: 1;
            transform: rotate(0) scale(1);
        }
        .dark .theme-toggle .sun-icon {
            opacity: 1;
            transform: rotate(0) scale(1);
        }
        .dark .theme-toggle .moon-icon {
            opacity: 0;
            transform: rotate(-90deg) scale(0);
        }

        /* Top Bar */
        .top-bar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }
        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-links {
            display: none;
            gap: 1.5rem;
        }
        @media (min-width: 768px) { .top-links { display: flex; } }
        .top-links a {
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s ease;
        }
        .top-links a:hover { color: var(--brand-accent); }

        /* Main Header */
        .main-header {
            background: var(--bg-secondary);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        .main-header.scrolled { box-shadow: var(--shadow); }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            gap: 1rem;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1.2rem;
        }
        .dark .logo-icon { color: #000; }
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .logo-text span:first-child { color: var(--brand-primary); }
        .logo-text span:last-child { color: var(--brand-accent); }

        /* Search Bar */
        .search-bar {
            display: none;
            flex: 1;
            max-width: 500px;
            position: relative;
        }
        @media (min-width: 768px) { .search-bar { display: block; } }
        .search-bar input {
            width: 100%;
            padding: 0.75rem 3rem 0.75rem 1.25rem;
            border-radius: 9999px;
            border: 1px solid var(--border-color);
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .search-bar input:focus {
            outline: none;
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        .dark .search-bar input:focus { box-shadow: 0 0 0 3px rgba(29, 91, 148, 0.1); }
        .search-bar button {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .dark .search-bar button { color: #000; }
        .search-bar button:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: var(--glow);
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .header-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            position: relative;
            transition: all 0.3s ease;
        }
        .header-btn:hover {
            background: var(--bg-tertiary);
            color: var(--brand-accent);
        }
        .header-btn .badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--brand-accent);
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dark .header-btn .badge { color: #000; }
        .login-btn {
            display: none;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        @media (min-width: 640px) { .login-btn { display: flex; } }
        .login-btn:hover {
            background: var(--brand-accent);
            color: white;
        }
        .dark .login-btn:hover { color: #000; }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-links">
                    <a href="<?= BASE_URL ?>/contact.php"><i class="fas fa-headset"></i> Support</a>
                    <a href="<?= BASE_URL ?>/track.php"><i class="fas fa-calendar-check"></i> Track Booking</a>
                    <a href="<?= BASE_URL ?>/faq.php"><i class="fas fa-question-circle"></i> FAQ</a>
                    <a href="<?= BASE_URL ?>/returns.php"><i class="fas fa-undo-alt"></i> Returns</a>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; color: var(--text-secondary);">
                    <span><i class="fas fa-globe"></i> EN</span>
                    <span>PHP</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header" id="header">
        <div class="container">
            <div class="header-content">
                <a href="<?= BASE_URL ?>/" class="logo">
                    <div class="logo-icon">N</div>
                    <div class="logo-text">
                        <span>Net</span><span>Hub</span>
                    </div>
                </a>

                <div class="search-bar">
                    <form action="<?= BASE_URL ?>/products.php" method="get">
                        <input type="text" name="search" placeholder="Search for routers, cables, services...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div class="header-actions">
                    <!-- Theme Toggle -->
                    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                        <i class="fas fa-sun sun-icon"></i>
                        <i class="fas fa-moon moon-icon"></i>
                    </button>

                    <button class="header-btn" style="display: none;">
                        <i class="fas fa-heart"></i>
                    </button>

                    <?php if ($isLoggedIn): ?>
                        <a href="<?= BASE_URL ?>/dashboard.php" class="header-btn">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login.php" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                
                </div>
            </div>
        </div>
    </header>