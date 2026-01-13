<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/config/database.php';

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User');
$role = $_SESSION['role'] ?? 'user';

// Fetch active lapangan
$lapangan = [];
try {
    $stmt = $conn->prepare('SELECT id, nama_lapangan, jenis, harga_per_jam, status FROM lapangan WHERE status = ? ORDER BY id ASC');
    $active = 'aktif';
    $stmt->bind_param('s', $active);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $lapangan[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    $lapangan = [];
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard - Booking Lapangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="site-title">Booking Lapangan</div>
            <nav>
                <span class="muted">Halo, <?php echo $user_name; ?></span>
                <a href="index.php">Beranda</a>
                <a href="riwayat.php">Riwayat</a>
                <?php if ($role === 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <h2>Daftar Lapangan</h2>
        <p class="muted">Pilih lapangan dan lakukan booking sesuai jadwal tersedia.</p>

        <?php if (empty($lapangan)): ?>
            <div class="card">Belum ada lapangan tersedia saat ini.</div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($lapangan as $l): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($l['nama_lapangan']); ?></h3>
                        <div class="meta"><?php echo htmlspecialchars($l['jenis']); ?></div>
                        <div class="price">Harga per jam: Rp <?php echo number_format((int)$l['harga_per_jam'], 0, ',', '.'); ?></div>
                        <div class="actions">
                            <a class="btn" href="booking.php?lapangan_id=<?php echo urlencode($l['id']); ?>">Book</a>
                            <a class="btn" href="booking.php?lapangan_id=<?php echo urlencode($l['id']); ?>#info">Info</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>