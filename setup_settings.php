<?php
// setup_settings.php
require 'config/db_connect.php';

try {
    echo "<h2>Fixing Missing Settings Table</h2>";
    echo "<p>Connected to database...</p>";

    // Create 'settings' table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT
    )";
    $pdo->exec($sql);

    echo "<p style='color:green'>✅ Successfully created 'settings' table.</p>";

    // Insert default values if empty
    $check = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($check == 0) {
        $defaults = [
            'site_title' => 'Midnight Cafe',
            'hero_title' => 'Taste the Art of Coffee',
            'hero_subtitle' => 'Where coding meets caffeine.',
            'footer_text' => 'Midnight Cyber Cafe',
            'social_facebook' => '#',
            'social_instagram' => '#'
        ];

        $insert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($defaults as $key => $val) {
            $insert->execute([$key, $val]);
        }
        echo "<p style='color:blue'>ℹ️ Inserted default settings.</p>";
    }

    echo "<p>You can now access the Site Settings page.</p>";
    echo "<a href='admin/settings.php'>Go to Site Settings</a>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>