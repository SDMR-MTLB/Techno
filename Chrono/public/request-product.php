<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/cart.php';
require_once __DIR__ . '/../core/validator.php';

$cart = cartGetItems();
$cartItems = [];
$total = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $db = DB::getConnection();
    $stmt = $db->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders) AND status = 'available'");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $productId = $product['id'];
        $cartItems[] = [
            'id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $cart[$productId]
        ];
        $total += $product['price'] * $cart[$productId];
    }
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'remove') {
        $removeId = (int)$_POST['product_id'];
        cartRemove($removeId);
        header('Location: request-product.php');
        exit;
    } elseif ($_POST['action'] === 'update') {
        foreach ($_POST['quantity'] as $productId => $qty) {
            $qty = (int)$qty;
            cartUpdate((int)$productId, $qty);
        }
        header('Location: request-product.php');
        exit;
    } elseif ($_POST['action'] === 'clear') {
        cartClear();
        header('Location: request-product.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Product – Pisowifi Vendo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="page-container">
    <h1 class="page-title">Request Products</h1>

    <?php if (empty($cartItems)): ?>
        <div class="empty-state">
            <p>Your cart is empty.</p>
            <a href="products.php" class="btn">Browse Products</a>
        </div>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="action" value="update">
            <table class="table-cart">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['image']): ?>
                                    <img src="<?= BASE_URL ?>/../uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>₱<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="0" max="99" class="quantity-input">
                            </td>
                            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            <td>
                                <button type="submit" formaction="?remove=<?= $item['id'] ?>" formmethod="post" name="action" value="remove" class="btn btn-small btn-danger">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-total">
                Total: ₱<?= number_format($total, 2) ?>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 30px;">
                <button type="submit" class="btn">Update Quantities</button>
                <button type="submit" formaction="?clear=1" formmethod="post" name="action" value="clear" class="btn btn-danger">Clear Cart</button>
            </div>
        </form>

        <div class="form-container">
            <h2 style="margin-bottom:20px;">Customer Information</h2>
            <form action="submit-product-request.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact Number *</label>
                    <input type="text" id="contact" name="contact" required>
                </div>
                <div class="form-group">
                    <label for="email">Email (optional)</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="address">Address / Location *</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Additional Notes (optional)</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <button type="submit" class="btn">Submit Request</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<div class="bottom-logo">LOGO</div>

</body>
</html>