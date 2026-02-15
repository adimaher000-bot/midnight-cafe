<?php
// update_inr_prices.php
require 'config/db_connect.php';

try {
    // Check if prices are already high (simple heuristic: if min price > 50, assume already INR)
    $stmt = $pdo->query("SELECT MIN(price) FROM menu");
    $min_price = $stmt->fetchColumn();

    if ($min_price > 50) {
        echo "Prices seem to be already in INR range (Min: $min_price). Skipping multiplication.";
    } else {
        // Multiply all prices by 83 (approx USD to INR rate) or set manual realistic values
        // Let's multiply by 80 for simplicity
        $sql = "UPDATE menu SET price = price * 80";
        $pdo->exec($sql);
        echo "All menu prices updated to INR (multiplied by 80).<br>";
    }

    // Create new sample items if they don't exist? 
    // Just verify
    $stmt = $pdo->query("SELECT * FROM menu");
    $items = $stmt->fetchAll();

    echo "<h3>Current Menu Prices:</h3><ul>";
    foreach ($items as $item) {
        echo "<li>{$item['item_name']} - ₹{$item['price']}</li>";
    }
    echo "</ul>";

    echo "<p><a href='index.php'>Go to Home</a></p>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>