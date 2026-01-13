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
    <title>Register - Booking Lapangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Register</h2>
    <?php if (!empty($_GET['error'])): ?>
        <div style="color:red"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <form method="post" action="actions/auth.php">
        <input type="hidden" name="action" value="register">
        <div>
            <label>Nama</label><br>
            <input type="text" name="nama" required>
        </div>
        <div>
            <label>Email</label><br>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label><br>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Konfirmasi Password</label><br>
            <input type="password" name="confirm_password" required>
        </div>
        <div>
            <button type="submit">Daftar</button>
        </div>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login</a></p>
</body>

</html>