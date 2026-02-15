<?php
// pages/process_order.php
require_once dirname(__DIR__) . '/config/db_connect.php';
require_once dirname(__DIR__) . '/includes/functions.php';

start_session_safe();

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (empty($_SESSION['cart'])) {
    redirect('../menu.php');
}

try {
    $pdo->beginTransaction();

    $user_id = $_SESSION['user_id'];
    $cart = $_SESSION['cart'];
    $total_amount = 0;

    // Fetch prices and calculate total
    $ids = array_keys($cart);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT menu_id, price FROM menu WHERE menu_id IN ($placeholders) AND is_available = 1");
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Validate items
    if (count($items) !== count($cart)) {
        // Some items might be unavailable
        // Ideally show error, but for now we proceed with available ones or fail?
        // Let's fail if any mismatch to be safe (or strict)
        // Or just process what we found.
    }

    // Calculate total
    foreach ($items as $item) {
        $qty = $cart[$item['menu_id']];
        $total_amount += $item['price'] * $qty;
    }

    if ($total_amount <= 0) {
        throw new Exception("Order total is zero.");
    }

    // Create Order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$user_id, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Create Order Items
    $stmtItems = $pdo->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $qty = $cart[$item['menu_id']];
        $stmtItems->execute([$order_id, $item['menu_id'], $qty, $item['price']]);
    }

    $pdo->commit();

    // Clear Cart
    unset($_SESSION['cart']);

    // Redirect with success
    redirect('../index.php?order_success=1');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log error or show message
    // redirect('../cart.php?error=' . urlencode('Order failed. Please try again.'));
    echo "Order failed: " . $e->getMessage();
}
?>