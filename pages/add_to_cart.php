<?php
// pages/add_to_cart.php
// Ensure no output before this
ob_start();

require_once dirname(__DIR__) . '/config/db_connect.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Clear any accidental output (like whitespace from includes)
ob_clean();

header('Content-Type: application/json');

try {
    start_session_safe();

    // Check if user is logged in
    // Note: isLoggedIn() calls start_session_safe() again, which is fine but we did it above to be sure.
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
        $menu_id = (int) $_POST['menu_id'];

        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or increment item
        if (isset($_SESSION['cart'][$menu_id])) {
            $_SESSION['cart'][$menu_id]++;
        } else {
            $_SESSION['cart'][$menu_id] = 1;
        }

        $cart_count = count($_SESSION['cart']); 

        echo json_encode(['success' => true, 'cart_count' => $cart_count, 'message' => 'Item added to cart!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}
