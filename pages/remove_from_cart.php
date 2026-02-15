<?php
// pages/remove_from_cart.php
require '../includes/functions.php';
start_session_safe();

if (isLoggedIn() && isset($_GET['id'])) {
    $menu_id = (int) $_GET['id'];
    if (isset($_SESSION['cart'][$menu_id])) {
        unset($_SESSION['cart'][$menu_id]);
    }
}
redirect('../cart.php');
?>