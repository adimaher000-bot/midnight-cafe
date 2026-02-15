<?php
// admin/login.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (isLoggedIn() && isAdmin()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = :email AND role = 'admin' LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        redirect('dashboard.php');
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <!-- Use the new admin theme with cache busting -->
    <link rel="stylesheet" href="../css/admin_theme.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;700&family=Outfit:wght@300;600&display=swap"
        rel="stylesheet">
</head>

<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">

    <div class="card" style="width: 100%; max-width: 400px; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 0.5rem;"><i class="fas fa-user-shield"></i> Admin Login</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Access the Midnight Console</p>

        <?php if ($error): ?>
            <p
                style="color: #ff6b6b; margin-bottom: 1rem; background: rgba(255,0,0,0.1); padding: 0.5rem; border-radius: 5px;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 1.5rem; text-align: left;">
                <label>Admin Email</label>
                <input type="email" name="email" placeholder="admin@example.com" required>
            </div>

            <div style="margin-bottom: 2rem; text-align: left;">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="adminPassword" placeholder="••••••••" required style="width: 100%; padding-right: 40px;">
                    <i class="fas fa-eye" id="toggleAdminPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="togglePasswordVisibility('adminPassword', 'toggleAdminPassword')"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                Enter Dashboard
            </button>
        </form>

        <a href="../index.php"
            style="display: inline-block; margin-top: 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
            &larr; Back to Website
        </a>
    </div>
    <script src="../js/script.js?v=<?php echo time(); ?>"></script>
</body>

</html>