<?php
// pages/update_cart.php
require '../includes/functions.php';
start_session_safe();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $menu_id = (int) $_POST['menu_id'];
    $quantity = (int) $_POST['quantity'];

    if ($quantity > 0 && isset($_SESSION['cart'][$menu_id])) {
        $_SESSION['cart'][$menu_id] = $quantity;
    }
}
redirect('../cart.php');
?>