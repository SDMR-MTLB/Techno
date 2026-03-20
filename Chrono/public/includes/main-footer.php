<?php
// public/includes/main-footer.php
?>
    <!-- Footer -->
    <footer class="footer">
        <!-- Newsletter -->
        <div class="newsletter">
            <div class="container">
                <div class="newsletter-content">
                    <div class="newsletter-text">
                        <h3>Stay in the loop</h3>
                        <p>Subscribe to get exclusive offers, product updates, and networking tips.</p>
                    </div>
                    <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Subscribed!');">
                        <input type="email" placeholder="Enter your email" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i> Subscribe</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <a href="<?= BASE_URL ?>/" class="logo">
                            <div class="logo-icon">N</div>
                            <div class="logo-text">
                                <span>Net</span><span>Hub</span>
                            </div>
                        </a>
                        <p>Your trusted partner for professional networking solutions. Quality products, expert services, and reliable support.</p>
                        <div class="social-links">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>

                    <div class="footer-column">
                        <h4>Customer Service</h4>
                        <ul>
                            <li><a href="<?= BASE_URL ?>/contact.php">Help Center</a></li>
                            <li><a href="<?= BASE_URL ?>/shipping.php">Shipping Policy</a></li>
                            <li><a href="<?= BASE_URL ?>/returns.php">Returns Policy</a></li>
                            <li><a href="<?= BASE_URL ?>/faq.php">FAQs</a></li>
                            <li><a href="<?= BASE_URL ?>/track.php">Order Tracking</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="<?= BASE_URL ?>/about.php">About Us</a></li>
                            <li><a href="<?= BASE_URL ?>/careers.php">Careers</a></li>
                            <li><a href="<?= BASE_URL ?>/press.php">Press</a></li>
                            <li><a href="<?= BASE_URL ?>/contact.php">Partnership</a></li>
                            <li><a href="#blog">Blog</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h4>Legal</h4>
                        <ul>
                            <li><a href="<?= BASE_URL ?>/privacy.php">Privacy Policy</a></li>
                            <li><a href="<?= BASE_URL ?>/terms.php">Terms of Service</a></li>
                            <li><a href="#cookies">Cookie Policy</a></li>
                            <li><a href="#disclaimer">Disclaimer</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h4>Contact</h4>
                        <ul>
                            <li><a href="mailto:support@nethub.ph"><i class="fas fa-envelope"></i> support@nethub.ph</a></li>
                            <li><a href="tel:+63281234567"><i class="fas fa-phone"></i> +63 2 8123 4567</a></li>
                            <li><a href="#location"><i class="fas fa-map-marker-alt"></i> Makati City, Philippines</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; <?= date('Y') ?> NetHub. All rights reserved. Quality networking solutions.</p>
                    <div class="footer-bottom-links">
                        <a href="<?= BASE_URL ?>/privacy.php">Privacy</a>
                        <a href="<?= BASE_URL ?>/terms.php">Terms</a>
                        <a href="#sitemap">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Additional footer styles */
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); }
        .newsletter { background: var(--bg-tertiary); padding: 3rem 0; }
        .newsletter-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem; }
        .newsletter-text h3 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .newsletter-text p { color: var(--text-secondary); }
        .newsletter-form { display: flex; gap: 0.5rem; }
        .newsletter-form input {
            padding: 0.75rem 1rem; border-radius: 9999px; border: 1px solid var(--border-color);
            background: var(--bg-secondary); color: var(--text-primary); min-width: 250px;
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem; border-radius: 9999px; border: none;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
            color: white; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;
        }
        .dark .newsletter-form button { color: #000; }
        .footer-main { padding: 4rem 0; }
        .footer-grid {
            display: grid; gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
        .footer-brand .logo { margin-bottom: 1rem; display: inline-flex; }
        .footer-brand p { color: var(--text-secondary); margin-bottom: 1.5rem; max-width: 300px; }
        .social-links { display: flex; gap: 1rem; }
        .social-links a {
            width: 2.5rem; height: 2.5rem; background: var(--bg-tertiary); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: var(--text-primary);
            transition: all 0.3s; text-decoration: none;
        }
        .social-links a:hover { background: var(--brand-accent); color: white; }
        .footer-column h4 { font-size: 1rem; margin-bottom: 1.5rem; }
        .footer-column ul { list-style: none; }
        .footer-column li { margin-bottom: 0.75rem; }
        .footer-column a {
            color: var(--text-secondary); text-decoration: none; transition: color 0.3s;
        }
        .footer-column a:hover { color: var(--brand-accent); }
        .footer-bottom {
            border-top: 1px solid var(--border-color); padding: 1.5rem 0;
        }
        .footer-bottom-content {
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;
        }
        .footer-bottom-links { display: flex; gap: 1.5rem; }
        .footer-bottom-links a { color: var(--text-secondary); text-decoration: none; }
        .footer-bottom-links a:hover { color: var(--brand-accent); }
    </style>

    <script>
        // Theme Toggle (only handle click, no initial reading)
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        themeToggle.addEventListener('click', () => {
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
});

        // Header scroll effect
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Slideshow functionality
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slideshow-dot');
        const prevBtn = document.getElementById('prevSlide');
        const nextBtn = document.getElementById('nextSlide');
        if (slides.length && dots.length && prevBtn && nextBtn) {
            let currentSlide = 0;
            let slideInterval;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
                currentSlide = index;
            }

            function nextSlide() {
                showSlide((currentSlide + 1) % slides.length);
            }

            function prevSlide() {
                showSlide((currentSlide - 1 + slides.length) % slides.length);
            }

            function startSlideshow() {
                slideInterval = setInterval(nextSlide, 5000);
            }

            function stopSlideshow() {
                clearInterval(slideInterval);
            }

            prevBtn.addEventListener('click', () => {
                prevSlide();
                stopSlideshow();
                startSlideshow();
            });

            nextBtn.addEventListener('click', () => {
                nextSlide();
                stopSlideshow();
                startSlideshow();
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    stopSlideshow();
                    startSlideshow();
                });
            });

            const slideshow = document.getElementById('slideshow');
            if (slideshow) {
                slideshow.addEventListener('mouseenter', stopSlideshow);
                slideshow.addEventListener('mouseleave', startSlideshow);
            }

            startSlideshow();
        }

        // Wishlist toggle
        document.querySelectorAll('.product-wishlist').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const icon = btn.querySelector('i');
                icon.classList.toggle('fas');
                icon.classList.toggle('far');
                btn.style.background = icon.classList.contains('fas') ? '#ef4444' : '';
                btn.style.color = icon.classList.contains('fas') ? 'white' : '';
            });
        });
    </script>
</body>
</html>