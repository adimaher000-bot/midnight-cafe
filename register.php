<?php
// register.php
require_once 'config/db_connect.php';
require_once 'includes/functions.php';

start_session_safe();

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

require 'includes/header.php';
?>

<div class="container glass-panel" style="max-width: 500px; padding: 2rem; margin-top: 3rem;">
    <h2 class="text-center">Register</h2>

    <?php if ($error): ?>
        <div style="background: rgba(255, 0, 0, 0.1); color: red; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div
            style="background: rgba(0, 255, 0, 0.1); color: green; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $success; ?>
        </div>
    <?php else: ?>

        <form method="POST">
            <div class="mb-4">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="mb-4">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="mb-4">
                <label>Phone Number</label>
                <input type="tel" name="phone" pattern="[0-9]{10}" maxlength="10" minlength="10" title="Please enter exactly 10 digits"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="10-digit mobile number">
            </div>
            <div class="mb-4">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="regPassword" required style="width: 100%; padding-right: 40px;">
                    <i class="fas fa-eye" id="toggleRegPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="togglePasswordVisibility('regPassword', 'toggleRegPassword')"></i>
                </div>
            </div>
            <div class="mb-4">
                <label>Confirm Password</label>
                <div style="position: relative;">
                    <input type="password" name="confirm_password" id="regConfirmPassword" required style="width: 100%; padding-right: 40px;">
                    <i class="fas fa-eye" id="toggleRegConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="togglePasswordVisibility('regConfirmPassword', 'toggleRegConfirmPassword')"></i>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
        </form>

        <p class="text-center" style="margin-top: 1rem;">
            Already have an account? <a href="login.php" style="color: var(--accent);">Login here</a>
        </p>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>