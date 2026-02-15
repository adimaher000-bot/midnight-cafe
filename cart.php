<?php
// cart.php
require 'config/db_connect.php';
require 'includes/functions.php'; // Include functions directly

start_session_safe();

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

require 'includes/header.php'; // Now output HTML


$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    if (!empty($ids)) {
        // Fetch details from DB
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM menu WHERE menu_id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map menu items to cart
        foreach ($products as $product) {
            $menu_id = $product['menu_id'];
            $quantity = $_SESSION['cart'][$menu_id];

            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;

            $total_price += $product['subtotal'];
            $cart_items[] = $product;
        }
    }
}
?>

<div class="container glass-panel" style="padding: 2rem;">
    <h1 class="text-center">Your Cart</h1>

    <?php if (empty($cart_items)): ?>
        <p class="text-center">Your cart is empty.</p>
        <div class="text-center">
            <a href="menu.php" class="btn btn-primary">Start Ordering</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($item['image'] ?: 'placeholder.jpg'); ?>" width="50"
                                style="vertical-align: middle; margin-right: 1rem;">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </td>
                        <td>
                            <?php echo format_price($item['price']); ?>
                        </td>
                        <td>
                            <form action="pages/update_cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="menu_id" value="<?php echo $item['menu_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1"
                                    style="width: 50px; padding: 0.25rem;">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i></button>
                            </form>
                        </td>
                        <td>
                            <?php echo format_price($item['subtotal']); ?>
                        </td>
                        <td>
                            <a href="pages/remove_from_cart.php?id=<?php echo $item['menu_id']; ?>" class="btn btn-accent"><i
                                    class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; padding: 1rem;">Total:</td>
                    <td style="padding: 1rem; font-weight: bold; color: var(--primary); font-size: 1.25rem;">
                        <?php echo format_price($total_price); ?>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div style="text-align: right; margin-top: 2rem;">
            <a href="pages/clear_cart.php" class="btn btn-accent" onclick="return confirm('Are you sure you want to clear your cart?');">Clear Cart</a>
            <a href="menu.php" class="btn btn-accent">Continue Shopping</a>
            <a href="pages/process_order.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>