<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once __DIR__ . '/../config/database.php';

$user_id = (int)$_SESSION['user_id'];
$lapangan_id = (int)($_POST['lapangan_id'] ?? 0);
$tanggal = $_POST['tanggal_booking'] ?? '';
$jam_mulai = $_POST['jam_mulai'] ?? '';
$jam_selesai = $_POST['jam_selesai'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');

if (!$lapangan_id || !$tanggal || !$jam_mulai || !$jam_selesai) {
    header('Location: ../booking.php?lapangan_id=' . urlencode($lapangan_id) . '&error=' . urlencode('Semua field wajib diisi'));
    exit();
}

if ($jam_selesai <= $jam_mulai) {
    header('Location: ../booking.php?lapangan_id=' . urlencode($lapangan_id) . '&error=' . urlencode('Jam selesai harus setelah jam mulai'));
    exit();
}

// Check lapangan exists and get price
$stmt = $conn->prepare('SELECT harga_per_jam FROM lapangan WHERE id = ? AND status = ? LIMIT 1');
$active = 'aktif';
$stmt->bind_param('is', $lapangan_id, $active);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    header('Location: ../dashboard.php');
    exit();
}
$harga = (int)$row['harga_per_jam'];
$stmt->close();

// Check conflict: any booking for same lapangan/date that overlaps
$conflict_sql = 'SELECT id FROM booking WHERE lapangan_id = ? AND tanggal_booking = ? AND status != "cancelled" AND NOT (jam_selesai <= ? OR jam_mulai >= ?) LIMIT 1';
$stmt = $conn->prepare($conflict_sql);
$stmt->bind_param('isss', $lapangan_id, $tanggal, $jam_mulai, $jam_selesai);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header('Location: ../booking.php?lapangan_id=' . urlencode($lapangan_id) . '&error=' . urlencode('Waktu bentrok dengan booking lain'));
    exit();
}
$stmt->close();

// Calculate total price (hours difference)
$start_ts = strtotime($jam_mulai);
$end_ts = strtotime($jam_selesai);
$hours = ($end_ts - $start_ts) / 3600;
if ($hours <= 0) $hours = 1;
$total = (int)ceil($hours * $harga);

// Insert booking
$stmt = $conn->prepare('INSERT INTO booking (user_id, lapangan_id, tanggal_booking, jam_mulai, jam_selesai, total_harga, status) VALUES (?,?,?,?,?,?,?)');
$status = 'pending';
$stmt->bind_param('iisssis', $user_id, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $total, $status);
if ($stmt->execute()) {
    header('Location: ../riwayat.php?success=' . urlencode('Booking berhasil.'));
    exit();
} else {
    header('Location: ../booking.php?lapangan_id=' . urlencode($lapangan_id) . '&error=' . urlencode('Gagal membuat booking'));
    exit();
}
