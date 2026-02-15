<?php
// setup_admin.php
require 'config/db_connect.php';

$email = 'admin@cafe.com';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        // Update
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);
        echo "Admin password reset to 'admin123'.";
    } else {
        // Create
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('Admin User', ?, ?, 'admin')");
        $stmt->execute([$email, $hashed_password]);
        echo "Admin account created. Email: $email, Password: $password";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>