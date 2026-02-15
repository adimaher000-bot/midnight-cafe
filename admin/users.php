<?php
// admin/users.php
require '../config/db_connect.php';
require '../includes/functions.php';

start_session_safe();

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/admin_theme.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>User Management</h1>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#
                                <?php echo $user['user_id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge" style="background: var(--primary); color: white;">Admin</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #17a2b8; color: white;">Customer</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $user['created_at']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>