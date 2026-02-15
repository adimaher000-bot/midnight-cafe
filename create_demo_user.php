<?php
// create_demo_user.php
require 'config/db_connect.php';

$email = 'user@cafe.com';
$password = 'user123';
$name = 'Demo Customer';

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        // Update password just in case
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ?, name = ?, role = 'customer' WHERE email = ?");
        $update->execute([$hashed, $name, $email]);
        echo "Demo user updated.<br>";
    } else {
        // Create new user
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
        $insert->execute([$name, $email, $hashed]);
        echo "Demo user created.<br>";
    }

    echo "<h3>Demo User Credentials:</h3>";
    echo "<strong>Username/Email:</strong> $email<br>";
    echo "<strong>Password:</strong> $password<br>";
    echo "<br><a href='login.php'>Go to Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>