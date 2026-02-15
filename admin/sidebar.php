<?php
// admin/sidebar.php
?>
<div class="sidebar">
    <div style="text-align: center; margin-bottom: 2rem;">
        <i class="fas fa-coffee"
            style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem; text-shadow: 0 0 15px rgba(212, 163, 115, 0.4);"></i>
        <h2 style="margin: 0; border: none; font-size: 1.5rem;">Midnight Admin</h2>
    </div>

    <a href="dashboard.php"
        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="orders.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i> Orders
    </a>
    <a href="menu.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">
        <i class="fas fa-utensils"></i> Menu Items
    </a>
    <a href="bookings.php"
        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
        <i class="fas fa-calendar-check"></i> Bookings
    </a>
    <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
        <i class="fas fa-users"></i> Users
    </a>
    <a href="settings.php"
        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
        <i class="fas fa-cog"></i> Site Settings
    </a>

    <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <a href="../index.php" class="nav-link" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Site
        </a>
        <a href="../logout.php" class="nav-link" style="color: #dc3545;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>