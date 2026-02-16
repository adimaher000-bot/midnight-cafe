<?php
// includes/header.php
require_once dirname(__DIR__) . '/includes/functions.php';

// Safe session start
start_session_safe();

$cart_count = 0;
if (isLoggedIn() && isset($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Midnight Cafe</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=<?php echo time(); ?>">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;700&family=Outfit:wght@300;600&family=Patrick+Hand&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <nav class="navbar glass-panel">
        <a href="<?php echo BASE_URL; ?>home.php" class="brand"
            style="font-family: var(--font-heading); font-size: 1.5rem; text-decoration:none; color:var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-coffee text-gold"></i>
            <?php echo get_setting('site_title', 'Midnight Cafe'); ?>
        </a>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Toggle Menu">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-links-center">
            <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-accent" style="border:none;">Home</a>
            <a href="<?php echo BASE_URL; ?>index.php#menu-section" class="btn btn-accent" style="border:none;">Menu</a>
            <a href="#" class="btn btn-accent" style="border:none;">About</a>
            <a href="#" class="btn btn-accent" style="border:none;">Services</a>
        </div>

        <div class="nav-links-right">
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo BASE_URL; ?>pages/my_orders.php" class="btn btn-accent" title="Orders"><i
                        class="fas fa-receipt"></i></a>
                <a href="<?php echo BASE_URL; ?>cart.php" class="btn btn-accent" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="cart-count-display"
                        style="background:var(--primary); color:black; padding:2px 6px; border-radius:50%; font-size:0.8rem;"><?php echo $cart_count; ?></span>
                </a>
                <div style="display:inline-block; position:relative;" class="dropdown">
                    <button class="btn btn-primary dropdown-toggle">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
                    </button>
                    <div class="dropdown-content glass-panel">
                        <a href="<?php echo BASE_URL; ?>pages/my_orders.php">My Orders</a>
                        <a href="<?php echo BASE_URL; ?>logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-accent">Login</a>
                <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Floating Cart Widget -->
    <a href="<?php echo BASE_URL; ?>cart.php" id="floating-cart" class="glass-panel" title="View Cart"
        style="display: none;">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count-display input-badge"><?php echo $cart_count; ?></span>
    </a>

    <style>
        /* Dropdown CSS */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--surface);
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: var(--radius-lg);
            padding: 0.5rem 0;
        }

        .dropdown-content a {
            color: var(--text-main);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: var(--primary);
            color: white;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
    <main class="container">