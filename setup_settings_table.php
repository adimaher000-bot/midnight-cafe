<?php
// setup_settings_table.php
require 'config/db_connect.php';

echo "Connected to database...\n";

$sql = file_get_contents('sql_fixed/create_settings.sql');

try {
    $pdo->exec($sql);
    echo "Settings table created/updated successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>