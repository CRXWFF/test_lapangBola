<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/config/database.php';

$bookings = [];
try {
    $sql = 'SELECT b.id, b.tanggal_booking, b.jam_mulai, b.jam_selesai, b.total_harga, b.status, b.created_at, l.nama_lapangan, u.nama as user_name, u.email
            FROM booking b
            JOIN lapangan l ON l.id = b.lapangan_id
            JOIN users u ON u.id = b.user_id
            ORDER BY b.tanggal_booking DESC, b.jam_mulai DESC';
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $bookings[] = $row;
    }
} catch (Exception $e) {
    $bookings = [];
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - Booking Lapangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="site-title">Booking Lapangan</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="riwayat.php">Riwayat</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <a href="index.php">← Kembali ke Beranda</a>
        <h2>Admin — Semua Booking</h2>

        <?php if (empty($bookings)): ?>
            <div class="card">Belum ada booking.</div>
        <?php else: ?>
            <?php foreach ($bookings as $b): ?>
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <strong><?php echo htmlspecialchars($b['nama_lapangan']); ?></strong>
                            <div class="muted"><?php echo htmlspecialchars($b['tanggal_booking']); ?> — <?php echo htmlspecialchars($b['jam_mulai']); ?> s.d. <?php echo htmlspecialchars($b['jam_selesai']); ?></div>
                            <div class="muted">User: <?php echo htmlspecialchars($b['user_name']); ?> (<?php echo htmlspecialchars($b['email']); ?>)</div>
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
                    <div style="margin-top:10px">
                        <?php if ($b['status'] === 'pending'): ?>
                            <form method="post" action="actions/confirm_booking.php" style="display:inline">
                                <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                <button class="btn" style="background:#28a745">Confirm</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="actions/cancel_booking.php" style="display:inline" onsubmit="return confirm('Batalkan booking ini?');">
                            <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                            <button class="btn" style="background:#dc3545">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>