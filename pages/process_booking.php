<?php
// pages/process_booking.php
require_once dirname(__DIR__) . '/config/db_connect.php';
require_once dirname(__DIR__) . '/includes/functions.php';

start_session_safe();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic fields
    $name = sanitize_input($_POST['name']);
    $phone = sanitize_input($_POST['phone']);
    $date = sanitize_input($_POST['date']);
    $time = sanitize_input($_POST['time']);
    $guests = (int) $_POST['guests'];
    // Optional
    $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;

    if (empty($name) || empty($phone) || empty($date) || empty($time)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, full_name, phone, booking_date, booking_time, guests, special_request, status) VALUES (?, ?, ?, ?, ?, ?, '', 'pending')");
        $stmt->execute([$user_id, $name, $phone, $date, $time, $guests]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>