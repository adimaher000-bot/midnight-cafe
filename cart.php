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

// Fetch Payment Settings
$settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('upi_id', 'qr_code_image')");
$payment_settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$upi_id = $payment_settings['upi_id'] ?? '9822699485@kotak811';
$qr_image = !empty($payment_settings['qr_code_image']) ? 'images/' . $payment_settings['qr_code_image'] : 'images/payment-qr.png';

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
                            <?php
                            $img_src = $item['image'] ?: 'placeholder.jpg';
                            if (!str_starts_with($img_src, 'data:')) {
                                $img_src = 'images/' . $img_src;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" width="50"
                                style="vertical-align: middle; margin-right: 1rem; border-radius: 5px;">
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
            <a href="pages/clear_cart.php" class="btn btn-accent"
                onclick="return confirm('Are you sure you want to clear your cart?');">Clear Cart</a>
            <a href="menu.php" class="btn btn-accent">Continue Shopping</a>
            <button onclick="openPaymentModal()" class="btn btn-primary">Proceed to Checkout</button>
        </div>
    <?php endif; ?>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal"
        style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center;">
        <div class="modal-content glass-panel"
            style="background-color: #1a0f00; margin: auto; padding: 2rem; border: 1px solid var(--primary); width: 90%; max-width: 400px; text-align: center; border-radius: 15px; position: relative; max-height: 90vh; overflow-y: auto;">
            <span class="close" onclick="closePaymentModal()"
                style="color: var(--primary); float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2 style="color: var(--primary); margin-bottom: 1.5rem; font-family: 'Fraunces', serif;">Scan to Pay</h2>

            <div
                style="background: white; padding: 1rem; display: inline-block; border-radius: 10px; margin-bottom: 1rem;">
                <!-- QR Code from Settings -->
                <img src="<?php echo htmlspecialchars($qr_image); ?>" alt="Payment QR Code"
                    style="width: 200px; height: 200px; object-fit: contain;">
            </div>

            <p style="color: var(--text-main); margin-bottom: 0.5rem; font-size: 1.1rem;">UPI ID:</p>
            <div
                style="background: rgba(255,255,255,0.1); padding: 0.5rem; border-radius: 5px; margin-bottom: 2rem; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <code
                    style="font-family: monospace; color: var(--primary); font-size: 1.2rem;"><?php echo htmlspecialchars($upi_id); ?></code>
                <button onclick="copyToClipboard('<?php echo htmlspecialchars($upi_id); ?>')"
                    style="background: none; border: none; color: var(--text-muted); cursor: pointer;"
                    title="Copy UPI ID">
                    <i class="fas fa-copy"></i>
                </button>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="closePaymentModal()" class="btn btn-accent">Cancel</button>
                <button onclick="confirmPayment()" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inline script for modal handling if not in main script.js yet
    function openPaymentModal() {
        document.getElementById('paymentModal').style.display = 'block';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    function confirmPayment() {
        // Redirect to process_order.php
        window.location.href = 'pages/process_order.php';
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('UPI ID copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        const modal = document.getElementById('paymentModal');
        if (event.target == modal) {
            closePaymentModal();
        }
    }
</script>

<?php require 'includes/footer.php'; ?>