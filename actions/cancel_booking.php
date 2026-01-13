<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once __DIR__ . '/../config/database.php';

$user_id = (int)$_SESSION['user_id'];
$booking_id = (int)($_POST['booking_id'] ?? 0);

if (!$booking_id) {
    header('Location: ../riwayat.php');
    exit();
}

// Verify booking belongs to user or user is admin
$stmt = $conn->prepare('SELECT user_id, status FROM booking WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    header('Location: ../riwayat.php');
    exit();
}

if ($row['status'] === 'cancelled') {
    header('Location: ../riwayat.php');
    exit();
}

if ($row['user_id'] !== $user_id && ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../riwayat.php');
    exit();
}

$stmt->close();

$stmt = $conn->prepare('UPDATE booking SET status = ? WHERE id = ?');
$cancel = 'cancelled';
$stmt->bind_param('si', $cancel, $booking_id);
$stmt->execute();

header('Location: ../riwayat.php?success=' . urlencode('Booking dibatalkan'));
exit();
