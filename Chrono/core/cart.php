<?php
// core/cart.php
require_once __DIR__ . '/session.php';

function cartInit() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cartAdd($productId, $quantity = 1) {
    cartInit();
    $productId = (int)$productId;
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function cartRemove($productId) {
    unset($_SESSION['cart'][$productId]);
}

function cartUpdate($productId, $quantity) {
    if ($quantity <= 0) {
        cartRemove($productId);
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function cartGetItems() {
    cartInit();
    return $_SESSION['cart'];
}

function cartClear() {
    $_SESSION['cart'] = [];
}

function cartCount() {
    cartInit();
    return array_sum($_SESSION['cart']);
}