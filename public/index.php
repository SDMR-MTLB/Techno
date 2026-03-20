<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';

$db = DB::getConnection();
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch top products (latest 5)
$topStmt = $db->query("SELECT id, name, description, price, image FROM products WHERE status = 'available' ORDER BY id DESC LIMIT 5");
$topProducts = $topStmt->fetchAll();

$popularStmt = $db->query("SELECT id, name, description, price, image FROM products WHERE status = 'available' ORDER BY id DESC LIMIT 10");
$popularProducts = $popularStmt->fetchAll();

$newStmt = $db->query("SELECT id, name, description, price, image FROM products WHERE status = 'available' ORDER BY id DESC LIMIT 4");
$newProducts = $newStmt->fetchAll();

// Fetch services
$servicesStmt = $db->query("SELECT id, name, description, estimated_price FROM services WHERE status = 'active' LIMIT 3");
$services = $servicesStmt->fetchAll();

// Get product counts per category
$catCounts = [];
$catStmt = $db->query("SELECT category, COUNT(*) as cnt FROM products WHERE status = 'available' GROUP BY category");
while ($row = $catStmt->fetch()) {
    $catCounts[$row['category']] = $row['cnt'];
}

// Define categories with display info and map to database slugs
$categories = [
    ['name' => 'Routers', 'icon' => 'wifi', 'desc' => 'Enterprise & home routing', 'slug' => 'routers', 'color' => 'from-blue-500 to-cyan-500'],
    ['name' => 'Access Points', 'icon' => 'broadcast-tower', 'desc' => 'Wi‑Fi coverage', 'slug' => 'access-points', 'color' => 'from-purple-500 to-pink-500'],
    ['name' => 'Firewalls', 'icon' => 'shield-alt', 'desc' => 'Security appliances', 'slug' => 'firewalls', 'color' => 'from-red-500 to-orange-500'],
    ['name' => 'Switches', 'icon' => 'network-wired', 'desc' => 'Managed switches', 'slug' => 'switches', 'color' => 'from-green-500 to-emerald-500'],
    ['name' => 'Cables', 'icon' => 'ethernet', 'desc' => 'Fiber & Ethernet', 'slug' => 'cables', 'color' => 'from-yellow-500 to-amber-500'],
    ['name' => 'Miscellaneous', 'icon' => 'microchip', 'desc' => 'Adapters & tools', 'slug' => 'misc', 'color' => 'from-indigo-500 to-violet-500'],
    ['name' => 'Repair', 'icon' => 'wrench', 'desc' => 'Repair services', 'slug' => 'repair', 'color' => 'from-rose-500 to-pink-500'],
    ['name' => 'Configuration', 'icon' => 'cogs', 'desc' => 'Expert setup', 'slug' => 'configuration', 'color' => 'from-teal-500 to-cyan-500'],
    ['name' => 'Packages', 'icon' => 'box', 'desc' => 'Installation bundles', 'slug' => 'packages', 'color' => 'from-orange-500 to-red-500'],
];

// Add count to each category
foreach ($categories as &$cat) {
    $cat['count'] = $catCounts[$cat['slug']] ?? 0;
}
unset($cat);

$pageTitle = 'Home';
include __DIR__ . '/includes/main-header.php';
?>

<!-- New design CSS (copied from the first example's header) -->
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

    /* Utility Classes */
    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    @media (min-width: 640px) { .container { padding: 0 1.5rem; } }
    @media (min-width: 1024px) { .container { padding: 0 2rem; } }

    /* Hero Section */
    .hero { padding: 2rem 0; background: var(--bg-secondary); }
    .hero-grid { display: grid; gap: 2rem; grid-template-columns: 1fr; }
    @media (min-width: 1024px) { .hero-grid { grid-template-columns: 1fr 0.8fr; } }
    .hero-badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(14, 165, 233, 0.1); color: var(--brand-accent);
        padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .hero-title { font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 1rem; }
    .hero-subtitle { font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 2rem; }

    /* Slideshow */
    .slideshow { position: relative; overflow: hidden; border-radius: 1rem; background: var(--card-bg); box-shadow: var(--shadow); }
    .slide { display: none; padding: 1.5rem; }
    .slide.active { display: block; }
    .slide-content { display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; }
    .slide-image { position: relative; width: 120px; height: 120px; }
    .slide-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem; }
    .slide-badge {
        position: absolute; top: -0.5rem; left: -0.5rem;
        background: var(--brand-accent); color: white; font-size: 0.75rem; font-weight: 600;
        padding: 0.25rem 0.75rem; border-radius: 9999px;
    }
    .slide-info { flex: 1; }
    .slide-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
    .slide-desc { color: var(--text-secondary); margin-bottom: 0.5rem; }
    .slide-price { font-size: 1.5rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 1rem; }
    .slideshow-nav { display: flex; justify-content: center; gap: 0.5rem; margin: 1rem 0; }
    .slideshow-dot {
        width: 0.5rem; height: 0.5rem; border-radius: 50%; background: var(--text-muted);
        border: none; cursor: pointer; transition: all 0.3s;
    }
    .slideshow-dot.active { background: var(--brand-accent); width: 1.5rem; border-radius: 0.25rem; }
    .slideshow-arrows {
        position: absolute; top: 50%; transform: translateY(-50%);
        display: flex; justify-content: space-between; width: 100%; padding: 0 1rem;
        pointer-events: none;
    }
    .slideshow-arrow {
        pointer-events: auto;
        background: transparent;
        border: 2px solid var(--brand-accent);
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        color: var(--brand-accent);
        box-shadow: none;
    }
    .slideshow-arrow:hover {
        background: var(--brand-accent);
        color: white;
    }

    /* Partner Card */
    .partner-card {
        background: linear-gradient(135deg, var(--bg-tertiary), var(--bg-secondary));
        padding: 2rem; border-radius: 1rem; border: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }
    .partner-badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: var(--brand-accent); color: white; padding: 0.5rem 1rem;
        border-radius: 9999px; font-size: 0.875rem; font-weight: 500; margin-bottom: 1rem;
    }
    .partner-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
    .partner-desc { color: var(--text-secondary); margin-bottom: 1.5rem; }
    .partner-benefits { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
    .partner-benefit { display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary); }

    /* Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .stat-card {
        background: var(--card-bg); padding: 1.5rem; border-radius: 0.75rem;
        display: flex; align-items: center; gap: 1rem; border: 1px solid var(--border-color);
    }
    .stat-icon {
        width: 3rem; height: 3rem; background: var(--bg-tertiary); border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--brand-accent);
    }
    .stat-value { font-size: 1.5rem; font-weight: 700; }
    .stat-label { color: var(--text-secondary); }

    /* Section Headers */
    .section { padding: 4rem 0; }
    .section-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
    }
    .section-title {
        font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem;
    }
    .section-title i {
        width: 2.5rem; height: 2.5rem; background: var(--bg-tertiary); border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
    }
    .view-all {
        color: var(--brand-accent); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;
        font-weight: 500; transition: gap 0.3s;
    }
    .view-all:hover { gap: 0.75rem; }

    /* Categories Grid */
    .categories-grid {
        display: grid; gap: 1rem; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
    .category-card {
        background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 1rem;
        padding: 1.5rem; text-decoration: none; color: inherit; transition: all 0.3s;
        display: flex; flex-direction: column;
    }
    .category-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--brand-accent); }
    .category-icon {
        width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center;
        justify-content: center; font-size: 1.25rem; color: white; margin-bottom: 1rem;
    }
    .bg-gradient-to-br { background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent)); }
    .category-card h3 { font-weight: 600; margin-bottom: 0.25rem; }
    .category-card p { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem; }
    .category-count {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: auto; color: var(--text-secondary); font-size: 0.875rem;
    }

    /* Services Grid */
    .services-grid {
        display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    .service-card {
        background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 1rem;
        padding: 1.5rem; position: relative;
    }
    .service-popular {
        position: absolute; top: 1rem; right: 1rem; background: #fbbf24; color: #000;
        padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
    }
    .service-icon {
        width: 3rem; height: 3rem; background: var(--bg-tertiary); border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: var(--brand-accent);
        margin-bottom: 1rem;
    }
    .service-name { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
    .service-desc { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem; }
    .service-features { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .service-feature { font-size: 0.75rem; color: var(--text-secondary); }
    .service-meta { display: flex; gap: 1rem; margin-bottom: 1rem; color: var(--text-secondary); font-size: 0.875rem; }
    .service-price-row {
        display: flex; justify-content: space-between; align-items: center;
        border-top: 1px solid var(--border-color); padding-top: 1rem;
    }
    .service-price-label { font-size: 0.75rem; color: var(--text-secondary); }
    .service-price { font-size: 1.25rem; font-weight: 700; color: var(--brand-primary); }

    /* Trust Badges */
    .trust-section {
        background: var(--bg-secondary); overflow: hidden; padding: 2rem 0;
        border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);
    }
    .trust-scroll {
        display: flex; gap: 2rem; animation: scroll 30s linear infinite;
        width: max-content;
    }
    .trust-badge {
        display: flex; align-items: center; gap: 0.75rem; background: var(--card-bg);
        padding: 0.75rem 1.5rem; border-radius: 9999px; border: 1px solid var(--border-color);
        white-space: nowrap;
    }
    .trust-badge i { color: var(--brand-accent); font-size: 1.25rem; }
    .trust-title { font-weight: 600; }
    .trust-desc { font-size: 0.75rem; color: var(--text-secondary); }
    @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

    /* Products Grid */
    .products-grid {
        display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
    .product-card {
        background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 1rem;
        overflow: hidden; position: relative; transition: all 0.3s;
    }
    .product-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
    .product-badge {
        position: absolute; top: 1rem; left: 1rem; background: var(--brand-accent);
        color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;
        font-weight: 600; z-index: 2;
    }
    .product-badge.featured { background: #f97316; }
    .product-wishlist {
        position: absolute; top: 1rem; right: 1rem; background: var(--card-bg);
        border: 1px solid var(--border-color); width: 2rem; height: 2rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        z-index: 2; transition: all 0.3s;
    }
    .product-wishlist:hover { background: #ef4444; color: white; border-color: #ef4444; }
    .product-image {
        height: 200px; background: var(--bg-tertiary); display: flex; align-items: center;
        justify-content: center; position: relative;
    }
    .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .product-affiliate {
        position: absolute; bottom: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.5);
        color: white; font-size: 0.65rem; padding: 0.15rem 0.5rem; border-radius: 9999px;
    }
    .product-info { padding: 1rem; }
    .product-name { font-weight: 600; margin-bottom: 0.25rem; }
    .product-desc { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem; }
    .product-rating { display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.5rem; }
    .stars { color: #fbbf24; }
    .rating-value { font-weight: 600; }
    .rating-count { color: var(--text-secondary); font-size: 0.875rem; }
    .product-price { display: flex; align-items: center; margin-bottom: 1rem; }
    .current-price { font-size: 1.25rem; font-weight: 700; color: var(--brand-primary); }
    .btn-add-cart {
        width: 100%; background: var(--brand-primary); color: white; border: none;
        padding: 0.75rem; border-radius: 9999px; font-weight: 600; cursor: pointer;
        transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-add-cart:hover { background: var(--brand-accent); }

    /* Features */
    .features-grid {
        display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    .feature-card {
        background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 1rem;
        padding: 1.5rem;
    }
    .feature-header { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .feature-icon {
        width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center;
        justify-content: center; font-size: 1.25rem; color: white;
    }
    .feature-title { font-weight: 600; margin-bottom: 0.25rem; }
    .feature-desc { color: var(--text-secondary); font-size: 0.875rem; }
    .feature-benefits { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
    .feature-benefit { font-size: 0.875rem; color: var(--text-secondary); }

    /* Section title wrapper (for badges) */
    .section-title-wrapper { display: flex; align-items: center; gap: 0.75rem; }
    .badge-affiliate {
        background: #ec4899; color: white; padding: 0.25rem 0.75rem; border-radius: 20px;
        font-size: 0.7rem; font-weight: 600;
    }
    .badge-new {
        background: #22c55e; color: white; padding: 0.25rem 0.75rem; border-radius: 20px;
        font-size: 0.7rem; font-weight: 600;
    }

    /* Button primary */
    .btn-primary {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
        color: white; padding: 0.75rem 1.5rem; border-radius: 9999px; text-decoration: none;
        display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;
        border: none; cursor: pointer; transition: all 0.3s;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--glow); }
    .dark .btn-primary { color: #000; }
    .gradient { background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
</style>

<!-- Hero Section (with slideshow) -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <div class="hero-badge">
                    <i class="fas fa-bolt"></i> Our Top Products
                </div>
                <h1 class="hero-title">
                    Professional <span class="gradient">Networking</span> Solutions
                </h1>
                <p class="hero-subtitle">
                    Enterprise‑grade network devices, configuration services, and installation packages — all in one place.
                </p>

                <!-- Slideshow -->
                <div class="slideshow" id="slideshow">
                    <?php if (count($topProducts) > 0): ?>
                        <?php foreach ($topProducts as $index => $product): ?>
                            <div class="slide <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                                <div class="slide-content">
                                    <div class="slide-image">
                                        <span class="slide-badge"><?= $product['id'] === 1 ? 'Best Seller' : 'Top' ?></span>
                                        <?php if ($product['image']): ?>
                                            <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                        <?php else: ?>
                                            <i class="fas fa-microchip" style="font-size:5rem; color:var(--text-muted);"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="slide-info">
                                        <h3 class="slide-title"><?= htmlspecialchars($product['name']) ?></h3>
                                        <p class="slide-desc"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 100)) ?>...</p>
                                        <div class="slide-price">₱<?= number_format($product['price'], 2) ?></div>
                                        <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn-primary">
                                            View Details <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="slide active">
                            <div class="slide-content">
                                <div class="slide-image">
                                    <span class="slide-badge">Welcome</span>
                                    <i class="fas fa-microchip" style="font-size:5rem; color:var(--text-muted);"></i>
                                </div>
                                <div class="slide-info">
                                    <h3 class="slide-title">Professional Networking Solutions</h3>
                                    <p class="slide-desc">Browse our latest products and services.</p>
                                    <a href="<?= BASE_URL ?>/products.php" class="btn-primary">Shop Now <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="slideshow-nav" id="slideshowDots">
                        <?php if (count($topProducts) > 0): ?>
                            <?php for ($i = 0; $i < count($topProducts); $i++): ?>
                                <button class="slideshow-dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>"></button>
                            <?php endfor; ?>
                        <?php else: ?>
                            <button class="slideshow-dot active"></button>
                        <?php endif; ?>
                    </div>

                    <div class="slideshow-arrows">
                        <button class="slideshow-arrow" id="prevSlide"><i class="fas fa-chevron-left"></i></button>
                        <button class="slideshow-arrow" id="nextSlide"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <div>
                <div class="partner-card">
                    <span class="partner-badge"><i class="fas fa-handshake"></i> Partner with Us</span>
                    <h3 class="partner-title">Be Our Partner!</h3>
                    <p class="partner-desc">Join our network of trusted installers and resellers. Get exclusive discounts and support.</p>

                    <div class="partner-benefits">
                        <div class="partner-benefit"><i class="fas fa-percent"></i> Exclusive Discounts</div>
                        <div class="partner-benefit"><i class="fas fa-headset"></i> Priority Support</div>
                        <div class="partner-benefit"><i class="fas fa-rocket"></i> Early Access</div>
                        <div class="partner-benefit"><i class="fas fa-bullhorn"></i> Co‑marketing</div>
                    </div>

                    <a href="<?= BASE_URL ?>/contact.php" class="btn-primary">
                        Contact Us <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-wifi"></i></div>
                        <div>
                            <div class="stat-value">10K+</div>
                            <div class="stat-label">Devices Sold</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <div class="stat-value">99.9%</div>
                            <div class="stat-label">Uptime SLA</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-network-wired"></i></div>
                        <div>
                            <div class="stat-value">500+</div>
                            <div class="stat-label">Partners</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-bolt"></i></div>
                        <div>
                            <div class="stat-value">24/7</div>
                            <div class="stat-label">Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-th-large"></i> Shop by Category
            </h2>
            <a href="<?= BASE_URL ?>/products.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>/products.php?category=<?= urlencode($cat['slug']) ?>" class="category-card">
                <div class="category-icon bg-gradient-to-br <?= $cat['color'] ?>">
                    <i class="fas fa-<?= $cat['icon'] ?>"></i>
                </div>
                <h3><?= $cat['name'] ?></h3>
                <p><?= $cat['desc'] ?></p>
                <div class="category-count">
                    <span><?= $cat['count'] ?> items</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-concierge-bell"></i> Popular Services & Packages
            </h2>
            <a href="<?= BASE_URL ?>/services.php" class="view-all">View All Services <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="services-grid">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $index => $service): ?>
                <div class="service-card">
                    <?php if ($index === 0 || $index === 2): ?>
                    <span class="service-popular"><i class="fas fa-star"></i> Popular</span>
                    <?php endif; ?>
                    <div class="service-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h3 class="service-name"><?= htmlspecialchars($service['name']) ?></h3>
                    <p class="service-desc"><?= htmlspecialchars(substr($service['description'] ?? '', 0, 80)) ?>...</p>

                    <div class="service-features">
                        <span class="service-feature"><i class="fas fa-check-circle"></i> Expert</span>
                        <span class="service-feature"><i class="fas fa-check-circle"></i> Warranty</span>
                        <span class="service-feature"><i class="fas fa-check-circle"></i> Support</span>
                    </div>

                    <div class="service-meta">
                        <span><i class="fas fa-clock"></i> 2‑4 hours</span>
                        <span><i class="fas fa-star" style="color: #fbbf24;"></i> 4.8</span>
                    </div>

                    <div class="service-price-row">
                        <div>
                            <div class="service-price-label">Starting from</div>
                            <div class="service-price">₱<?= number_format($service['estimated_price'] ?? 0, 0) ?></div>
                        </div>
                        <a href="<?= BASE_URL ?>/booking-service.php?service_id=<?= $service['id'] ?>" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                            Book Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static services -->
                <div class="service-card">
                    <span class="service-popular"><i class="fas fa-star"></i> Popular</span>
                    <div class="service-icon"><i class="fas fa-network-wired"></i></div>
                    <h3 class="service-name">Router Configuration</h3>
                    <p class="service-desc">Professional setup for your office network.</p>
                    <div class="service-price-row">
                        <div><div class="service-price-label">Starting from</div><div class="service-price">₱1,500</div></div>
                        <a href="<?= BASE_URL ?>/booking-service.php" class="btn-primary" style="padding: 0.5rem 1rem;">Book Now</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-tools"></i></div>
                    <h3 class="service-name">Repair Service</h3>
                    <p class="service-desc">Diagnose and fix hardware issues.</p>
                    <div class="service-price-row">
                        <div><div class="service-price-label">Starting from</div><div class="service-price">₱800</div></div>
                        <a href="<?= BASE_URL ?>/booking-service.php" class="btn-primary" style="padding: 0.5rem 1rem;">Book Now</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-box-open"></i></div>
                    <h3 class="service-name">SOHO Installation</h3>
                    <p class="service-desc">Complete setup for small offices.</p>
                    <div class="service-price-row">
                        <div><div class="service-price-label">Starting from</div><div class="service-price">₱5,000</div></div>
                        <a href="<?= BASE_URL ?>/packages.php" class="btn-primary" style="padding: 0.5rem 1rem;">View</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section class="trust-section">
    <div class="trust-scroll">
        <?php
        $trustBadges = [
            ['icon' => 'award', 'title' => 'Certified Technicians', 'desc' => 'Industry‑certified professionals'],
            ['icon' => 'truck', 'title' => 'On‑site Service', 'desc' => 'We come to you'],
            ['icon' => 'stethoscope', 'title' => 'Free Diagnostics', 'desc' => 'Complimentary assessment'],
            ['icon' => 'tachometer-alt', 'title' => 'Fast Delivery', 'desc' => 'Same‑day in Metro Manila'],
            ['icon' => 'undo-alt', 'title' => 'Easy Returns', 'desc' => '30‑day policy'],
            ['icon' => 'shield-check', 'title' => 'Warranty Protected', 'desc' => 'Full coverage'],
            ['icon' => 'headset', 'title' => '24/7 Support', 'desc' => 'Always available'],
            ['icon' => 'clock', 'title' => 'Quick Response', 'desc' => 'Under 2 hours'],
        ];
        // Duplicate for seamless scroll
        foreach (array_merge($trustBadges, $trustBadges) as $badge):
        ?>
        <div class="trust-badge">
            <i class="fas fa-<?= $badge['icon'] ?>"></i>
            <div class="trust-text">
                <div class="trust-title"><?= $badge['title'] ?></div>
                <div class="trust-desc"><?= $badge['desc'] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Popular Products -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-title-wrapper">
                <h2 class="section-title">
                    <i class="fas fa-fire" style="background: rgba(249, 115, 22, 0.1); color: #f97316;"></i> Popular Networking Hardware
                </h2>
                <span class="badge-affiliate">Affiliate Links</span>
            </div>
        </div>

        <div class="products-grid">
            <?php if (count($popularProducts) > 0): ?>
                <?php foreach ($popularProducts as $product): ?>
                <div class="product-card">
                    <span class="product-badge featured">Featured</span>
                    <button class="product-wishlist"><i class="fas fa-heart"></i></button>
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-microchip" style="font-size:3rem; color:var(--text-muted);"></i>
                        <?php endif; ?>
                        <span class="product-affiliate"><i class="fas fa-external-link-alt"></i> Affiliate</span>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 40)) ?>...</p>
                        <div class="product-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></span>
                            <span class="rating-value">4.5</span>
                            <span class="rating-count">(128)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">₱<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <button class="btn-add-cart" onclick="window.location.href='<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>'">
                            <i class="fas fa-shopping-cart"></i> View Details
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <div class="section-title-wrapper">
                <h2 class="section-title">
                    <i class="fas fa-sparkles" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;"></i> Just Arrived
                </h2>
                <span class="badge-new">New Stock</span>
            </div>
            <a href="<?= BASE_URL ?>/products.php" class="view-all">View All New <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="products-grid">
            <?php if (count($newProducts) > 0): ?>
                <?php foreach ($newProducts as $product): ?>
                <div class="product-card">
                    <span class="product-badge" style="background: #22c55e;"><i class="fas fa-sparkles"></i> NEW</span>
                    <button class="product-wishlist"><i class="fas fa-heart"></i></button>
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?= BASE_URL ?>/../uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-microchip" style="font-size:3rem; color:var(--text-muted);"></i>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 40)) ?>...</p>
                        <div class="product-price" style="justify-content: space-between; align-items: center;">
                            <span class="current-price">₱<?= number_format($product['price'], 0) ?></span>
                            <button class="btn-add-cart" style="width: auto; padding: 0.4rem 1rem;" onclick="window.location.href='<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>'">
                                <i class="fas fa-shopping-cart"></i> View
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No new products.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section">
    <div class="container">
        <div class="section-header text-center" style="text-align:center;">
            <div>
                <h2 class="section-title" style="font-size:2rem;">Why Choose <span class="gradient">NetHub</span></h2>
                <p style="color: var(--text-secondary); max-width: 600px; margin: 1rem auto;">
                    We go beyond just selling products. Our comprehensive services ensure your network infrastructure is always running at its best.
                </p>
            </div>
        </div>

        <div class="features-grid">
            <?php
            $features = [
                ['icon' => 'headset', 'title' => 'After Sales Services', 'desc' => 'We support you even after purchase with comprehensive warranty coverage and dedicated technical assistance.', 'color' => 'from-blue-500 to-cyan-500', 'benefits' => ['24/7 Support', 'Warranty Claims', 'Tech Assistance', 'Remote Help']],
                ['icon' => 'cogs', 'title' => 'Device Configuration', 'desc' => 'Professional setup of routers, switches, firewalls, and other network devices optimized for peak performance.', 'color' => 'from-purple-500 to-pink-500', 'benefits' => ['Router Setup', 'Switch Config', 'Firewall Rules', 'VPN Setup']],
                ['icon' => 'search', 'title' => 'Network Diagnostics', 'desc' => 'Identify and resolve connectivity issues with our expert diagnostic tools and actionable recommendations.', 'color' => 'from-green-500 to-emerald-500', 'benefits' => ['Speed Testing', 'Bottleneck Analysis', 'Security Audit', 'Reports']],
                ['icon' => 'tools', 'title' => 'Installation Services', 'desc' => 'On‑site installation for complete networking setups from small offices to enterprise deployments.', 'color' => 'from-orange-500 to-red-500', 'benefits' => ['On‑site Setup', 'Cabling', 'Device Mounting', 'Training']],
            ];
            foreach ($features as $feature):
            ?>
            <div class="feature-card">
                <div class="feature-header">
                    <div class="feature-icon bg-gradient-to-br <?= $feature['color'] ?>">
                        <i class="fas fa-<?= $feature['icon'] ?>"></i>
                    </div>
                    <div>
                        <h3 class="feature-title"><?= $feature['title'] ?></h3>
                        <p class="feature-desc"><?= $feature['desc'] ?></p>
                    </div>
                </div>
                <div class="feature-benefits">
                    <?php foreach ($feature['benefits'] as $benefit): ?>
                    <div class="feature-benefit"><i class="fas fa-check-circle"></i> <?= $benefit ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/main-footer.php'; ?>