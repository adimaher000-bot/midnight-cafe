<?php
// update_mojito_image.php
require 'config/db_connect.php';

$itemName = 'Blue Galaxy Mojito';
$imageName = 'blue_galaxy_mojito.webp'; // Assuming WEBP format from generator

// Check if item exists
$stmt = $pdo->prepare("SELECT menu_id FROM menu WHERE item_name = ?");
$stmt->execute([$itemName]);
$item = $stmt->fetch();

if ($item) {
    // Update image
    $update = $pdo->prepare("UPDATE menu SET image = ? WHERE menu_id = ?");
    if ($update->execute([$imageName, $item['menu_id']])) {
        echo "Successfully updated image for '$itemName' to '$imageName'.";
    } else {
        echo "Failed to update image.";
    }
} else {
    echo "Item '$itemName' not found in menu.";
}
?>