<?php
// reset_admin_force.php
$host = 'localhost';
$db = 'cafe_db';
$user = 'root';
$pass = ''; // Default XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = 'admin@cafe.com';
    $password = 'admin123';
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Delete existing admin to be safe
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute([$email]);

    // Create fresh admin
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('Top Admin', ?, ?, 'admin')");
    $stmt->execute([$email, $hashed]);

    echo "<h1>Admin Account Reset</h1>";
    echo "<p>Admin user has been forcefully reset.</p>";
    echo "<ul>";
    echo "<li>Email: <strong>$email</strong></li>";
    echo "<li>Password: <strong>$password</strong></li>";
    echo "</ul>";
    echo "<p><a href='admin/login.php'>Login Here</a></p>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>