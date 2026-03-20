<?php
// public/includes/header.php
$currentPage = basename($_SERVER['SCRIPT_NAME']); // e.g., 'products.php'
?>
<header>
    <nav class="navbar">
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/index.php" <?= $currentPage == 'index.php' ? 'class="active"' : '' ?>>Home</a>
            <a href="<?= BASE_URL ?>/products.php" <?= $currentPage == 'products.php' ? 'class="active"' : '' ?>>Products</a>
            <a href="<?= BASE_URL ?>/services.php" <?= $currentPage == 'services.php' ? 'class="active"' : '' ?>>Services</a>
            <a href="<?= BASE_URL ?>/request-product.php" <?= $currentPage == 'request-product.php' ? 'class="active"' : '' ?>>Request Product</a>
            <a href="<?= BASE_URL ?>/request-service.php" <?= $currentPage == 'request-service.php' ? 'class="active"' : '' ?>>Request Service</a>
            <a href="<?= BASE_URL ?>/contact.php" <?= $currentPage == 'contact.php' ? 'class="active"' : '' ?>>Contact</a>
        </div>
    </nav>
</header>