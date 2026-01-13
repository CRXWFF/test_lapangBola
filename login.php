<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Booking Lapangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Login</h2>
    <?php if (!empty($_GET['error'])): ?>
        <div style="color:red"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
        <div style="color:green"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <form method="post" action="actions/auth.php">
        <input type="hidden" name="action" value="login">
        <div>
            <label>Email</label><br>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label><br>
            <input type="password" name="password" required>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</body>

</html>