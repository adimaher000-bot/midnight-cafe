<?php
// admin/orders.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = htmlspecialchars($_POST['order_id']);
    $status = htmlspecialchars($_POST['status']);

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$status, $order_id]);
    // Refresh to show update
    header("Location: orders.php");
    exit();
}

// Fetch Orders
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sql = "SELECT orders.*, users.name as user_name, users.email 
        FROM orders 
        JOIN users ON orders.user_id = users.user_id 
        ORDER BY order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/admin_theme.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>All Orders</h1>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Items</th>
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
                                <?php echo htmlspecialchars($order['user_name']); ?><br>
                                <small>
                                    <?php echo htmlspecialchars($order['email']); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo $order['order_date']; ?>
                            </td>
                            <td>$
                                <?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <!-- Fetch items for this order (simplified for demo, ideally fetch all at once) -->
                                <?php
                                $msg = '';
                                $stmtCallback = $pdo->prepare("SELECT menu.item_name, order_items.quantity FROM order_items JOIN menu ON order_items.menu_id = menu.menu_id WHERE order_items.order_id = ?");
                                $stmtCallback->execute([$order['order_id']]);
                                $items = $stmtCallback->fetchAll();
                                foreach ($items as $i) {
                                    echo htmlspecialchars($i['quantity'] . 'x ' . $i['item_name']) . '<br>';
                                }
                                ?>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status" onchange="this.form.submit()" style="padding: 0.25rem;">
                                        <option value="pending" <?php if ($order['status'] == 'pending')
                                            echo 'selected'; ?>>
                                            Pending</option>
                                        <option value="preparing" <?php if ($order['status'] == 'preparing')
                                            echo 'selected'; ?>>Preparing</option>
                                        <option value="completed" <?php if ($order['status'] == 'completed')
                                            echo 'selected'; ?>>Completed</option>
                                        <option value="cancelled" <?php if ($order['status'] == 'cancelled')
                                            echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>