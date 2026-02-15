<?php
// reset_demo_account.php
require 'config/db_connect.php';

// Force these credentials
$email = 'user@cafe.com';
$password = 'user123';
$name = 'Demo Customer';

try {
    // 1. Delete any existing user with this email
    $del = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $del->execute([$email]);
    echo "Deleted any existing user with email: $email<br>";

    // 2. Create the user fresh
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    // Explicitly verify hash creation
    if (!$hashed) {
        die("Password hashing failed!");
    }

    $insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
    $insert->execute([$name, $email, $hashed]);

    // 3. Verify it exists
    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);
    $user = $check->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<h2 style='color:green'>Success! Demo User Reset.</h2>";
        echo "<strong>Use these EXACT credentials:</strong><br>";
        echo "Email/Username: <code>$email</code><br>";
        echo "Password: <code>$password</code><br>";
        echo "Stored Hash: " . substr($user['password'], 0, 10) . "...<br>";

        // 4. Test Verification
        if (password_verify($password, $user['password'])) {
            echo "<br><strong>Self-Check:</strong> Password verifies correctly! ✅<br>";
        } else {
            echo "<br><strong style='color:red'>Self-Check:</strong> Password verification FAILED! ❌<br>";
        }

        echo "<br><a href='login.php'>Go to Login Page</a>";
    } else {
        echo "<h2 style='color:red'>Failed to create user!</h2>";
    }

} catch (PDOException $e) {
    echo "<h2>Database Error:</h2>" . $e->getMessage();
}
?>