<?php
// setup_database.php

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ''; // Default XAMPP password is empty

try {
    // Connect without database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.<br>";

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cafe_db");
    echo "Database 'cafe_db' checked/created.<br>";

    // Select Database
    $pdo->exec("USE cafe_db");

    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/sql_fixed/init.sql');

    // Execute SQL (might need to split by semicolon if multiple statements not supported in one go by PDO depending on driver, but usually okay for simple dumps or we split)
    // PDO exec supports multi-statements if configured but often it's safer to split.
    // However, init.sql is simple enough. Let's try splitting by ;

    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    echo "Database tables created/imported successfully.<br>";

    // Initialize Admin
    // Re-use logic or call existing file? Let's just do it here for simplicitly
    $admin_email = 'admin@cafe.com';
    $password = 'admin123';
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->execute([$admin_email]);
    if (!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('Admin User', ?, ?, 'admin')");
        $insert->execute([$admin_email, $hashed]);
        echo "Admin user ($admin_email) created.<br>";
    } else {
        // Admin user exists, ensure password is set to admin123
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$hashed, $admin_email]);
        echo "Admin user ($admin_email) password reset to 'admin123'.<br>";
    }

    echo "<h3>Setup Complete!</h3>";
    echo "<p><a href='index.php'>Go to Website</a></p>";

    // Run Settings Setup
    echo "<hr>";
    // We can't easily include setup_settings.php because it requires db_connect.php which creates a NEW connection.
    // Instead, let's run the settings logic directly here or redirect.
    // Better: Allow setup_settings.php to be run independently or via redirect.
    // Let's just add a link for now, or better yet, duplicate the critical logic here to ensure it's done.

    // Create 'settings' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT
    )");
    echo "Settings table checked/created.<br>";

    // Insert defaults
    $check_settings = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($check_settings == 0) {
        $defaults = [
            'site_title' => 'Midnight Cafe',
            'hero_title' => 'Taste the Art of Coffee',
            'hero_subtitle' => 'Where coding meets caffeine.',
            'footer_text' => 'Midnight Cyber Cafe',
            'social_facebook' => '#',
            'social_instagram' => '#'
        ];
        $insert_setting = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($defaults as $key => $val) {
            $insert_setting->execute([$key, $val]);
        }
        echo "Default settings inserted.<br>";
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "<br>Please ensure MySQL is running in XAMPP.");
}
?>