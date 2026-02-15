<?php
// pages/clear_cart.php
require_once dirname(__DIR__) . '/includes/functions.php';

start_session_safe();

if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect back to cart page
redirect('../cart.php');
?>
