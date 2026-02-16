<?php
// admin/menu.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_item'])) {
        $name = sanitize_input($_POST['item_name']);
        $desc = sanitize_input($_POST['description']);
        $price = (float) $_POST['price'];
        $discount = (int) $_POST['discount_percent'];
        $category = sanitize_input($_POST['category']);
        $sort = (int) $_POST['sort_order'];
        $image = $_POST['current_image'] ?? '';

        // Handle Image Upload (Base64)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_type = $_FILES['image']['type'];
            $data = file_get_contents($file_tmp);
            $base64 = base64_encode($data);
            $image = 'data:' . $file_type . ';base64,' . $base64;
        }

        if (isset($_POST['menu_id']) && !empty($_POST['menu_id'])) {
            // Update
            $sql = "UPDATE menu SET item_name=?, description=?, price=?, discount_percent=?, category=?, sort_order=?, image=? WHERE menu_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $desc, $price, $discount, $category, $sort, $image, $_POST['menu_id']]);
            $success = "Item updated successfully.";
        } else {
            // Check for duplicates (Add Mode)
            $check = $pdo->prepare("SELECT COUNT(*) FROM menu WHERE item_name = ?");
            $check->execute([$name]);
            if ($check->fetchColumn() > 0) {
                $error = "Error: Item '$name' already exists in the menu!";
            } else {
                // Insert
                $sql = "INSERT INTO menu (item_name, description, price, discount_percent, category, sort_order, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $desc, $price, $discount, $category, $sort, $image]);
                $success = "Item added successfully.";
            }
        }
        $action = 'list'; // Return to list after save
    } elseif (isset($_POST['toggle_availability'])) {
        $id = $_POST['menu_id'];
        $status = $_POST['status'] == 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE menu SET is_available = ? WHERE menu_id = ?");
        $stmt->execute([$status, $id]);
        header("Location: menu.php");
        exit();
    } elseif (isset($_POST['delete_item'])) {
        $id = $_POST['menu_id'];
        $stmt = $pdo->prepare("DELETE FROM menu WHERE menu_id = ?");
        $stmt->execute([$id]);
        $success = "Item deleted.";
    } elseif (isset($_POST['auto_arrange'])) {
        // Simple alpha sort by resetting sort_order
        // Or re-sort by category then name?
        $items = $pdo->query("SELECT menu_id FROM menu ORDER BY category, item_name")->fetchAll();
        $order = 1;
        foreach ($items as $item) {
            $pdo->prepare("UPDATE menu SET sort_order = ? WHERE menu_id = ?")->execute([$order++, $item['menu_id']]);
        }
        $success = "Menu auto-arranged alphabetically.";
    }
}

// Fetch Item for Edit
$item = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
    $stmt->execute([$_GET['id']]);
    $item = $stmt->fetch();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="../css/admin_theme.css?v=<?php echo time(); ?>">
    <!-- FontAwesome from CDN for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Menu Management</h1>

        <?php if ($success): ?>
            <p style="color: green; background: #d4edda; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $success; ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; background: #f8d7da; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
                <a href="?action=add" class="btn btn-primary"
                    style="padding: 0.5rem 1rem; background: var(--primary); color: white; text-decoration: none; border-radius: 5px;">+
                    Add New Item</a>
                <form method="POST" style="margin: 0;">
                    <button type="submit" name="auto_arrange" class="btn"
                        style="padding: 0.5rem 1rem; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">Auto
                        Arrange</button>
                </form>
                <button class="btn"
                    style="padding: 0.5rem 1rem; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Save
                    Layout</button>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Final</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $menu_items = $pdo->query("SELECT * FROM menu ORDER BY sort_order ASC")->fetchAll();
                        foreach ($menu_items as $m):
                            $final = $m['price'] * (1 - ($m['discount_percent'] / 100));
                            ?>
                            <tr>
                                <td><input type="number" value="<?php echo $m['sort_order']; ?>" style="width: 50px;"></td>
                                <td>
                                    <?php if ($m['image']): ?>
                                        <img src="<?php echo get_image_src($m['image']); ?>" width="50" height="50"
                                            style="object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <span style="color: #ccc;">No Img</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($m['item_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($m['category']); ?></small>
                                </td>
                                <td>₹<?php echo number_format($m['price'], 2); ?></td>
                                <td>
                                    <?php if ($m['discount_percent'] > 0): ?>
                                        <span style="color: red;">-<?php echo $m['discount_percent']; ?>%</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><strong>₹<?php echo number_format($final, 2); ?></strong></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="menu_id" value="<?php echo $m['menu_id']; ?>">
                                        <input type="hidden" name="status" value="<?php echo $m['is_available']; ?>">
                                        <button type="submit" name="toggle_availability"
                                            style="border:none; background:none; cursor:pointer;">
                                            <?php if ($m['is_available']): ?>
                                                <i class="fas fa-toggle-on" style="color: green; font-size: 1.5rem;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-toggle-off" style="color: #ccc; font-size: 1.5rem;"></i>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $m['menu_id']; ?>"
                                        style="color: blue; margin-right: 0.5rem;"><i class="fas fa-edit"></i></a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this item?');">
                                        <input type="hidden" name="menu_id" value="<?php echo $m['menu_id']; ?>">
                                        <button type="submit" name="delete_item"
                                            style="color: red; border: none; background: none; cursor: pointer;"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>

            <div class="card" style="max-width: 600px;">
                <h2><?php echo $action === 'edit' ? 'Edit Item' : 'Add New Item'; ?></h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="save_item" value="1">
                    <?php if ($item): ?>
                        <input type="hidden" name="menu_id" value="<?php echo $item['menu_id']; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $item['image']; ?>">
                    <?php endif; ?>

                    <div style="margin-bottom: 1rem;">
                        <label>Item Name</label>
                        <input type="text" name="item_name" value="<?php echo $item['item_name'] ?? ''; ?>" required>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label>Category</label>
                        <select name="category" required>
                            <?php
                            $cats = ['Starters', 'Main Course', 'Breads', 'Desserts', 'Beverages', 'Coffee', 'Snacks']; // Added new categories from spec
                            foreach ($cats as $c) {
                                $sel = ($item && $item['category'] === $c) ? 'selected' : '';
                                echo "<option value='$c' $sel>$c</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div style="margin-bottom: 1rem; flex: 1;">
                            <label>Price (₹)</label>
                            <input type="number" step="0.01" name="price" value="<?php echo $item['price'] ?? ''; ?>"
                                required>
                        </div>
                        <div style="margin-bottom: 1rem; flex: 1;">
                            <label>Discount (%)</label>
                            <input type="number" name="discount_percent"
                                value="<?php echo $item['discount_percent'] ?? '0'; ?>" min="0" max="100">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo $item['sort_order'] ?? '0'; ?>">
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?php echo $item['description'] ?? ''; ?></textarea>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label>Image</label>
                        <?php if ($item && $item['image']): ?>
                            <div style="margin-bottom: 0.5rem;">
                                <img src="<?php echo get_image_src($item['image']); ?>" width="100">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image">
                    </div>

                    <button type="submit" class="btn btn-primary"
                        style="background: var(--primary); color: #1A0F00; border: none; padding: 0.75rem 1.5rem; cursor: pointer; font-weight: bold;">Save
                        Item</button>
                    <a href="menu.php" style="margin-left: 1rem; color: var(--text-muted);">Cancel</a>
                </form>
            </div>

        <?php endif; ?>
    </div>

</body>

</html>