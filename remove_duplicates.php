<?php
// remove_duplicates.php
require 'config/db_connect.php';

echo "Checking for duplicate menu items...<br>";

// Find duplicates
$sql = "SELECT item_name, COUNT(*) as c FROM menu GROUP BY item_name HAVING c > 1";
$duplicates = $pdo->query($sql)->fetchAll();

if (empty($duplicates)) {
    echo "No duplicates found.<br>";
} else {
    echo "Found " . count($duplicates) . " duplicate item names.<br>";

    foreach ($duplicates as $dup) {
        $name = $dup['item_name'];
        echo "Processing '$name'...<br>";

        // Get all IDs for this item, ordered by ID ASC (oldest first)
        $stmt = $pdo->prepare("SELECT menu_id FROM menu WHERE item_name = ? ORDER BY menu_id ASC");
        $stmt->execute([$name]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Keep the first one, delete the rest
        $keep_id = array_shift($ids); // Removes entries from array, returns the first one

        if (!empty($ids)) {
            $ids_str = implode(',', $ids);
            echo "Keeping ID: $keep_id. Deleting IDs: $ids_str<br>";

            // Delete
            $deleteSql = "DELETE FROM menu WHERE menu_id IN ($ids_str)";
            $pdo->exec($deleteSql);
        }
    }
    echo "Cleanup complete!<br>";
}

echo "<a href='admin/menu.php'>Return to Menu Management</a>";
?>