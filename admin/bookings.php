<?php
// admin/bookings.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = htmlspecialchars($_POST['booking_id']);
    $status = htmlspecialchars($_POST['status']);

    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->execute([$status, $booking_id]);
    header("Location: bookings.php");
    exit();
}

$bookings = $pdo->query("SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="../css/admin_theme.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Table Reservations</h1>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Request</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#
                                <?php echo $booking['booking_id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($booking['full_name']); ?><br>
                                <small>
                                    <?php echo htmlspecialchars($booking['phone']); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($booking['booking_date'] . ' ' . $booking['booking_time']); ?>
                            </td>
                            <td>
                                <?php echo $booking['guests']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($booking['special_request']); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <button name="status" value="confirmed" class="btn-action btn-confirm">Confirm</button>
                                        <button name="status" value="cancelled" class="btn-action btn-cancel">Cancel</button>
                                    <?php else: ?>
                                        <select name="status" onchange="this.form.submit()" style="padding: 0.25rem;">
                                            <option value="pending" <?php if ($booking['status'] == 'pending')
                                                echo 'selected'; ?>>Pending</option>
                                            <option value="confirmed" <?php if ($booking['status'] == 'confirmed')
                                                echo 'selected'; ?>>Confirmed</option>
                                            <option value="cancelled" <?php if ($booking['status'] == 'cancelled')
                                                echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                    <?php endif; ?>
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