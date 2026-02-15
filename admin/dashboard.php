<?php
// admin/dashboard.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Stats from spec: Total Users, Total Menu Items, Today's Orders
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_menu_items = $pdo->query("SELECT COUNT(*) FROM menu")->fetchColumn();
$todays_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()")->fetchColumn();

// Recent Activity: Last 5 orders
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_theme.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1 style="margin-bottom: 2rem; font-size: 2.5rem;">Dashboard Overview</h1>

        <div class="stats-grid">

            <!-- Total Users -->
            <div class="card stat-card" style="border-left-color: #17a2b8;">
                <div class="stat-icon" style="color: #17a2b8;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h2 style="color: #17a2b8;"><?php echo $total_users; ?></h2>
                    <p>Total Users</p>
                </div>
            </div>

            <!-- Total Menu Items -->
            <div class="card stat-card" style="border-left-color: var(--primary);">
                <div class="stat-icon" style="color: var(--primary);">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-info">
                    <h2 style="color: var(--primary);"><?php echo $total_menu_items; ?></h2>
                    <p>Menu Items</p>
                </div>
            </div>

            <!-- Today's Orders -->
            <div class="card stat-card" style="border-left-color: #28a745;">
                <div class="stat-icon" style="color: #28a745;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h2 style="color: #28a745;"><?php echo $todays_orders; ?></h2>
                    <p>New Orders</p>
                </div>
            </div>

        </div>

        <h2>Recent Activity</h2>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['user_id']; ?></td>
                            <td><?php echo date('M d, H:i', strtotime($order['order_date'])); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <?php
                                $badgeClass = 'pending';
                                if ($order['status'] == 'completed')
                                    $badgeClass = 'completed';
                                if ($order['status'] == 'cancelled')
                                    $badgeClass = 'cancelled';
                                ?>
                                <span class="badge badge-<?php echo $badgeClass; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top: 1rem; text-align: right;">
                <a href="orders.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">View All
                    Orders &rarr;</a>
            </div>
        </div>

    </div>

</body>

</html>