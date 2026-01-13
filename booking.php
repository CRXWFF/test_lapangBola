<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/config/database.php';

$user_id = $_SESSION['user_id'];
$lapangan_id = isset($_GET['lapangan_id']) ? (int)$_GET['lapangan_id'] : 0;

$lapangan = null;
if ($lapangan_id > 0) {
    $stmt = $conn->prepare('SELECT id, nama_lapangan, jenis, harga_per_jam, status FROM lapangan WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $lapangan_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $lapangan = $res->fetch_assoc();
    $stmt->close();
}

if (!$lapangan) {
    header('Location: dashboard.php');
    exit();
}

?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Booking - <?php echo htmlspecialchars($lapangan['nama_lapangan']); ?></title>
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
        <a href="index.php">← Kembali ke Beranda</a>
        <h2>Booking: <?php echo htmlspecialchars($lapangan['nama_lapangan']); ?></h2>
        <div class="card">
            <div><strong>Jenis:</strong> <?php echo htmlspecialchars($lapangan['jenis']); ?></div>
            <div><strong>Harga per jam:</strong> Rp <?php echo number_format((int)$lapangan['harga_per_jam'], 0, ',', '.'); ?></div>

            <form method="post" action="actions/booking_process.php">
                <input type="hidden" name="lapangan_id" value="<?php echo (int)$lapangan['id']; ?>">
                <label>Tanggal booking</label>
                <input type="date" name="tanggal_booking" required min="<?php echo date('Y-m-d'); ?>">

                <div class="row">
                    <div>
                        <label>Jam mulai</label>
                        <input type="time" name="jam_mulai" required>
                    </div>
                    <div>
                        <label>Jam selesai</label>
                        <input type="time" name="jam_selesai" required>
                    </div>
                </div>

                <label>Catatan (opsional)</label>
                <input type="text" name="catatan" placeholder="Contoh: untuk latihan tim A">

                <div class="actions">
                    <button type="submit" class="btn">Konfirmasi Booking</button>
                    <a class="btn" href="dashboard.php" style="background:#6c757d">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>