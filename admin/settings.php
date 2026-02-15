<?php
// admin/settings.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$success = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_title' => $_POST['site_title'],
        'hero_title' => $_POST['hero_title'],
        'hero_subtitle' => $_POST['hero_subtitle'],
        'footer_text' => $_POST['footer_text'],
        'social_facebook' => $_POST['social_facebook'],
        'social_instagram' => $_POST['social_instagram'],
        'featured_item_id' => $_POST['featured_item_id']
    ];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
        $pdo->commit();
        $success = "Settings updated successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to update settings: " . $e->getMessage();
    }
}

// Fetch Current Settings
$current_settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while ($row = $stmt->fetch()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}
// Helper to safely get value for form
function val($key)
{
    global $current_settings;
    return isset($current_settings[$key]) ? htmlspecialchars($current_settings[$key]) : '';
}

// Fetch Menu Items for "Featured Item" Dropdown
$menu_items = $pdo->query("SELECT menu_id, item_name FROM menu ORDER BY item_name")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Site Settings</title>
    <link rel="stylesheet" href="../css/admin_theme.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Site Settings</h1>

        <?php if ($success): ?>
            <p style="color: green; background: #d4edda; padding: 1rem; border-radius: 5px;">
                <?php echo $success; ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; background: #f8d7da; padding: 1rem; border-radius: 5px;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="card" style="max-width: 800px;">
            <h2 style="margin-top: 0;">General Settings</h2>

            <div style="margin-bottom: 1rem;">
                <label>Website Title (Brand Name)</label>
                <input type="text" name="site_title" value="<?php echo val('site_title'); ?>" required
                    style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
            </div>

            <h2 style="margin-top: 2rem;">Hero Section</h2>
            <div style="margin-bottom: 1rem;">
                <label>Hero Title (Main Heading)</label>
                <input type="text" name="hero_title" value="<?php echo val('hero_title'); ?>" required
                    style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label>Hero Subtitle</label>
                <input type="text" name="hero_subtitle" value="<?php echo val('hero_subtitle'); ?>" required
                    style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
            </div>

            <h2 style="margin-top: 2rem;">Featured Section</h2>
            <div style="margin-bottom: 1rem;">
                <label>Signature Item (displayed with video)</label>
                <select name="featured_item_id" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
                    <?php foreach ($menu_items as $item): ?>
                        <option value="<?php echo $item['menu_id']; ?>" <?php if (val('featured_item_id') == $item['menu_id'])
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h2 style="margin-top: 2rem;">Footer</h2>
            <div style="margin-bottom: 1rem;">
                <label>Footer Text</label>
                <input type="text" name="footer_text" value="<?php echo val('footer_text'); ?>" required
                    style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
            </div>
            <div style="display: flex; gap: 1rem;">
                <div style="flex: 1; margin-bottom: 1rem;">
                    <label>Facebook Link (# for none)</label>
                    <input type="text" name="social_facebook" value="<?php echo val('social_facebook'); ?>"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
                </div>
                <div style="flex: 1; margin-bottom: 1rem;">
                    <label>Instagram Link (# for none)</label>
                    <input type="text" name="social_instagram" value="<?php echo val('social_instagram'); ?>"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ddd;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"
                style="background: var(--primary); color: white; border: none; padding: 1rem 2rem; cursor: pointer; font-size: 1.1rem; border-radius: 5px; margin-top: 1rem;">Save
                Settings</button>
        </form>
    </div>

</body>

</html>