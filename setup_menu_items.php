<?php
// setup_menu_items.php
require 'config/db_connect.php';

$items = [
    // The Midnight Hits (Top Rated)
    ['The Midnight Hits (Top Rated)', 'Chicken Tikka Sliders', 'Juicy chicken in toasted mini buns', 210, 0],
    ['The Midnight Hits (Top Rated)', 'Antigravity Makhani Fries', 'Crispy fries drowned in butter gravy', 120, 0],
    ['The Midnight Hits (Top Rated)', 'Cold Coffee with Ice Cream', 'The ultimate late-night boost', 150, 0],
    ['The Midnight Hits (Top Rated)', 'Gulab Jamun Cheesecake', 'A fusion masterpiece', 160, 0],

    // Midnight Bites (Starters & Fusion)
    ['Midnight Bites (Starters & Fusion)', 'Peri-Peri Paneer Tikka', '', 170, 0],
    ['Midnight Bites (Starters & Fusion)', 'Loaded Pizza Samosas', '', 80, 0],
    ['Midnight Bites (Starters & Fusion)', 'Veg Manchurian (Dry)', '', 140, 0],
    ['Midnight Bites (Starters & Fusion)', 'Aloo Tikki Burger', '', 90, 0],
    ['Midnight Bites (Starters & Fusion)', 'Classic Grilled Cheese Sandwich', '', 130, 0],

    // Cold Clouds (Specialty Shakes & Lassis)
    ['Cold Clouds (Shakes & Lassis)', 'Mango Lassi Shake', '', 120, 0],
    ['Cold Clouds (Shakes & Lassis)', 'Sweet Rose Lassi', '', 90, 0],
    ['Cold Clouds (Shakes & Lassis)', 'Hazelnut Frappé', '', 180, 0],
    ['Cold Clouds (Shakes & Lassis)', 'Blue Galaxy Mojito', '', 140, 0],
    ['Cold Clouds (Shakes & Lassis)', 'Iced Caramel Macchiato', '', 170, 0],

    // The Chiller (Canned & Bottled Drinks)
    ['The Chiller (Drinks)', 'Coca-Cola (Can)', '', 60, 0],
    ['The Chiller (Drinks)', 'Thums Up (Can)', '', 60, 0],
    ['The Chiller (Drinks)', 'Sprite (Can)', '', 60, 0],
    ['The Chiller (Drinks)', 'Diet Coke', '', 70, 0],
    ['The Chiller (Drinks)', 'Coke Zero', '', 70, 0],
    ['The Chiller (Drinks)', 'Appy Fizz', '', 50, 0],
    ['The Chiller (Drinks)', 'Red Bull', '', 160, 0],
    ['The Chiller (Drinks)', 'Monster Energy', '', 160, 0],
    ['The Chiller (Drinks)', 'Fresh Nimbu Soda', '', 70, 0],
    ['The Chiller (Drinks)', 'Masala Chaas', '', 50, 10], // 10% Off
    ['The Chiller (Drinks)', 'Jeera Soda', '', 60, 0],
    ['The Chiller (Drinks)', 'Bottled Water', '', 20, 0],

    // The Night Shift (Hot Drinks)
    ['The Night Shift (Hot Drinks)', 'Artisanal Filter Coffee', '', 60, 0],
    ['The Night Shift (Hot Drinks)', 'Adrak-Elaichi Masala Chai', '', 40, 0],
    ['The Night Shift (Hot Drinks)', 'Hot Chocolate with Marshmallows', '', 120, 0],

    // Sweet Dreams (Desserts)
    ['Sweet Dreams (Desserts)', 'Rasmalai Mousse', '', 110, 0],
    ['Sweet Dreams (Desserts)', 'Warm Gajar Halwa with Ice Cream', '', 130, 0],
    ['Sweet Dreams (Desserts)', 'Sizzling Brownie', '', 190, 0],
    ['Sweet Dreams (Desserts)', 'Nutella Filled Donuts', '', 120, 0],
];

echo "Adding menu items...<br>";

foreach ($items as $item) {
    list($cat, $name, $desc, $price, $discount) = $item;

    // Check availability
    $check = $pdo->prepare("SELECT COUNT(*) FROM menu WHERE item_name = ?");
    $check->execute([$name]);

    if ($check->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO menu (item_name, description, price, discount_percent, category, image, is_available) VALUES (?, ?, ?, ?, ?, NULL, 1)");
        if ($stmt->execute([$name, $desc, $price, $discount, $cat])) {
            echo "Added: $name<br>";
        } else {
            echo "Failed: $name<br>";
        }
    } else {
        echo "Skipped (Exists): $name<br>";
    }
}

echo "Done!";
?>