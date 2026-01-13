<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/config/database.php';

$user_id = (int)$_SESSION['user_id'];

$bookings = [];
try {
    $sql = 'SELECT b.id, b.tanggal_booking, b.jam_mulai, b.jam_selesai, b.total_harga, b.status, b.created_at, l.nama_lapangan
            FROM booking b
            JOIN lapangan l ON l.id = b.lapangan_id
            WHERE b.user_id = ?
            ORDER BY b.tanggal_booking DESC, b.jam_mulai DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $bookings[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    $bookings = [];
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Riwayat Booking</title>
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
        <h2>Riwayat Booking</h2>

        <?php if (empty($bookings)): ?>
            <div class="card">Belum ada riwayat booking.</div>
        <?php else: ?>
            <?php foreach ($bookings as $b): ?>
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <strong><?php echo htmlspecialchars($b['nama_lapangan']); ?></strong>
                            <div class="muted"><?php echo htmlspecialchars($b['tanggal_booking']); ?> — <?php echo htmlspecialchars($b['jam_mulai']); ?> s.d. <?php echo htmlspecialchars($b['jam_selesai']); ?></div>
                        </div>
                        <div style="text-align:right">
                            <div>Rp <?php echo number_format((int)$b['total_harga'], 0, ',', '.'); ?></div>
                            <?php if ($b['status'] === 'pending'): ?>
                                <div class="status-pending">PENDING</div>
                            <?php elseif ($b['status'] === 'confirmed'): ?>
                                <div class="status-confirmed">CONFIRMED</div>
                            <?php else: ?>
                                <div class="status-cancelled">CANCELLED</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="margin-top:10px;color:#666;font-size:0.95rem">Dipesan pada: <?php echo htmlspecialchars($b['created_at']); ?></div>
                    <?php if ($b['status'] === 'pending'): ?>
                        <form class="inline" method="post" action="actions/cancel_booking.php" onsubmit="return confirm('Batalkan booking ini?');">
                            <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                            <button class="btn" type="submit" style="background:#dc3545">Cancel</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>