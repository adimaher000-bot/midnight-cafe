<?php
// update_schema.php
require 'config/db_connect.php';

try {
    echo "<h2>Database Schema Update</h2>";
    echo "<p>Connected to database...</p>";

    // Change 'image' column in 'menu' table to LONGTEXT
    $sql = "ALTER TABLE menu MODIFY COLUMN image LONGTEXT";
    $pdo->exec($sql);

    echo "<p style='color:green'>✅ Successfully updated 'menu' table: 'image' column is now LONGTEXT.</p>";
    echo "<p>You can now upload images in the Admin Panel and they will save correctly.</p>";
    echo "<a href='admin/menu.php'>Go to Admin Menu</a>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>