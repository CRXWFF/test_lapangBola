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
    <div class="container">
        <header>
            <div class="site-title">Booking Lapangan</div>
            <nav>
                <a href="index.php">Beranda</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="riwayat.php">Riwayat</a>
                    <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </header>
    </div>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Register</h2>
            <?php if (!empty($_GET['error'])): ?>
                <div style="color:red;margin-bottom:8px;text-align:center"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form method="post" action="actions/auth.php">
                <input type="hidden" name="action" value="register">

                <label>Nama</label>
                <input type="text" name="nama" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required>

                <div class="auth-actions">
                    <button type="submit">Daftar</button>
                </div>
            </form>

            <div class="auth-note">Sudah punya akun? <a href="login.php">Login</a></div>
        </div>
    </div>
</body>

</html>