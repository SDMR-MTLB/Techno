<?php
// admin/partials/footer.php
// Closes main tag, includes footer and common scripts
?>
        </main> <!-- end main content -->

        <!-- Footer -->
        <footer class="px-4 sm:px-6 py-4 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 text-center">
                © <?= date('Y') ?> <?= ($_SESSION['admin_role'] ?? 'partner') === 'super' ? 'SuperAdmin' : 'Partner Program' ?>. All rights reserved.
            </p>
        </footer>
    </div> <!-- end main wrapper -->

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }
        
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        });
        
        // User Menu Toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', () => {
            userMenu.classList.add('hidden');
        });
        
        // Mobile sidebar
        const body = document.body;
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');

        function openSidebar() { body.classList.add('sidebar-open'); }
        function closeSidebar() { body.classList.remove('sidebar-open'); }

        if (menuBtn) {
            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                openSidebar();
            });
        }

        overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                body.classList.remove('sidebar-open');
            }
        });
    </script>
</body>
</html>