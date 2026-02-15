<?php
// login.php
require 'config/db_connect.php';
require 'includes/functions.php';

start_session_safe();

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Allow login by Email OR Name (Username)
        $sql = "SELECT * FROM users WHERE email = :u_email OR name = :u_name LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['u_email' => $username, 'u_name' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                session_write_close();
                redirect('admin/dashboard.php');
            } else {
                session_write_close();
                redirect('index.php');
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

require 'includes/header.php';
?>

<div style="display: flex; height: 80vh; align-items: center; justify-content: center; background: var(--bg-dark);">
    <div class="glass-panel"
        style="width: 100%; max-width: 400px; padding: 2rem; border-radius: var(--radius-lg); text-align: center;">
        <h2 style="margin-bottom: 2rem;">Login to Account</h2>

        <?php if ($error): ?>
            <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4 text-left">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="mb-4 text-left">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="loginPassword" required style="width: 100%; padding-right: 40px;">
                    <i class="fas fa-eye" id="toggleLoginPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="togglePasswordVisibility('loginPassword', 'toggleLoginPassword')"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login Now</button>
        </form>

        <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; font-size: 0.9rem;">
            <a href="register.php" style="color: var(--primary); text-decoration: none;">Don't have an account?
                Register</a>
            <a href="#" style="color: #888; text-decoration: none;">Forgot Password?</a>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>