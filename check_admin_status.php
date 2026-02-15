<?php
// check_admin_status.php
require 'config/db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT user_id, name, email, role FROM users WHERE role = 'admin'");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>Admin Users Found: " . count($admins) . "</h1>";

    if (count($admins) > 0) {
        echo "<ul>";
        foreach ($admins as $admin) {
            echo "<li><strong>Name:</strong> " . htmlspecialchars($admin['name']) . " | <strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</li>";
        }
        echo "</ul>";
        echo "<p>Please ensure you use the known credentials ('admin123') for these accounts.</p>";

        // Check specifically for admin@cafe.com
        $found = false;
        foreach ($admins as $a) {
            if ($a['email'] === 'admin@cafe.com')
                $found = true;
        }

        if (!$found) {
            echo "<p style='color:red'>Warning: Standard 'admin@cafe.com' not found. <a href='reset_admin_force.php'>Reset Admin Here</a></p>";
        } else {
            echo "<p style='color:green'>Standard Admin (admin@cafe.com) exists. Login with password 'admin123'.</p>";
        }

    } else {
        echo "<p style='color:red'>No admin users found! <a href='reset_admin_force.php'>Create Default Admin</a></p>";
    }

    echo "<br><a href='login.php'>Go to Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>