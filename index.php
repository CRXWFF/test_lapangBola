<?php
session_start();
require_once __DIR__ . '/config/database.php';

function e($v)
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

// Ambil list lapangan aktif
$lapangan = [];
if (isset($conn)) {
    $stmt = $conn->prepare("SELECT id, nama_lapangan, jenis, harga_per_jam FROM lapangan WHERE status = 'aktif'");
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $lapangan[] = $r;
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Booking Lapangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 20px;
            background: #f7f7f7
        }

        .container {
            max-width: 1000px;
            margin: 0 auto
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px
        }

        .site-title {
            font-size: 22px;
            font-weight: 700
        }

        nav a {
            margin-left: 12px;
            color: #333;
            text-decoration: none
        }

        .hero {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 16px
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px
        }

        .card {
            background: #fff;
            padding: 14px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06)
        }

        .card h3 {
            margin-bottom: 6px
        }

        .price {
            font-weight: 700;
            color: #0b6
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            background: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none
        }
    </style>
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

        <section class="hero">
            <h1>Selamat datang di sistem booking lapangan</h1>
            <p>Pilih lapangan yang tersedia, pilih tanggal & jam, lalu konfirmasi booking Anda.</p>
        </section>

        <section>
            <h2>Daftar Lapangan</h2>
            <?php if (empty($lapangan)): ?>
                <div class="card">Belum ada lapangan tersedia.</div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($lapangan as $l): ?>
                        <div class="card">
                            <h3><?php echo e($l['nama_lapangan']); ?></h3>
                            <div>Jenis: <?php echo e($l['jenis']); ?></div>
                            <div class="price">Rp <?php echo number_format($l['harga_per_jam'], 0, ',', '.'); ?> / jam</div>
                            <div style="margin-top:10px">
                                <a class="btn" href="booking.php?lapangan_id=<?php echo e($l['id']); ?>">Booking</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
    <script src="assets/js/script.js"></script>
</body>

</html>