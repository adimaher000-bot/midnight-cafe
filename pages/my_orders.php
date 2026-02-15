<?php
// pages/my_orders.php
require '../config/db_connect.php';
require '../includes/functions.php';
require '../includes/header.php'; // Correct path

start_session_safe();

if (!isLoggedIn()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Handle Order Cancellation
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    // Verify ownership and pending status
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();

    if ($order && $order['status'] === 'pending') {
        $update = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
        $update->execute([$order_id]);
        echo "<script>alert('Order #$order_id cancelled.'); window.location.href='my_orders.php';</script>";
    }
}

// Fetch Orders
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Fetch Bookings
$sqlBooking = "SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC";
$stmtBooking = $pdo->prepare($sqlBooking);
$stmtBooking->execute([$user_id]);
$bookings = $stmtBooking->fetchAll();
?>

<div class="container" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem;">My Order History</h1>

    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <div class="glass-panel" style="padding: 1rem; overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Items</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#
                                <?php echo $order['order_id']; ?>
                            </td>
                            <td>
                                <?php
                                $stmtItems = $pdo->prepare("SELECT m.item_name, oi.quantity FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id WHERE oi.order_id = ?");
                                $stmtItems->execute([$order['order_id']]);
                                $items = $stmtItems->fetchAll();
                                foreach ($items as $i) {
                                    echo htmlspecialchars($i['quantity'] . 'x ' . $i['item_name']) . '<br>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                            </td>
                            <td style="font-weight: bold;">₹
                                <?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td>
                                <?php
                                $statusColor = '#ffc107'; // Pending (Orange-Gold)
                                if ($order['status'] === 'completed')
                                    $statusColor = '#28a745'; // Green
                                if ($order['status'] === 'cancelled')
                                    $statusColor = '#dc3545'; // Red
                                if ($order['status'] === 'preparing')
                                    $statusColor = '#17a2b8'; // Cyan
                                ?>
                                <span
                                    style="color: <?php echo $statusColor; ?>; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($order['status'] === 'pending'): ?>
                                    <form method="POST" onsubmit="return confirm('Cancel this order?');">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" name="cancel_order" class="btn btn-accent"
                                            style="padding: 0.5rem; background: red; height: auto; width: auto;"
                                            title="Cancel Order">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h2 style="margin-top: 3rem; margin-bottom: 1rem;">Table Reservations</h2>
    <?php if (empty($bookings)): ?>
        <p>No active reservations.</p>
    <?php else: ?>
        <div class="glass-panel" style="padding: 1rem; overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>#
                                <?php echo $b['booking_id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($b['booking_date'] . ' ' . $b['booking_time']); ?>
                            </td>
                            <td>
                                <?php echo $b['guests']; ?>
                            </td>
                            <td>
                                <span
                                    style="color: <?php echo $b['status'] == 'confirmed' ? '#28a745' : ($b['status'] == 'cancelled' ? '#dc3545' : '#ffc107'); ?>; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                                    <?php echo ucfirst($b['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require '../includes/footer.php'; ?>