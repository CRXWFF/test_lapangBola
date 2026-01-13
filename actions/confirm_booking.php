<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit();
}
require_once __DIR__ . '/../config/database.php';

$booking_id = (int)($_POST['booking_id'] ?? 0);
if (!$booking_id) {
    header('Location: ../admin.php');
    exit();
}

$stmt = $conn->prepare('SELECT status FROM booking WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    header('Location: ../admin.php');
    exit();
}

if ($row['status'] === 'confirmed') {
    header('Location: ../admin.php');
    exit();
}

$stmt = $conn->prepare('UPDATE booking SET status = ? WHERE id = ?');
$confirmed = 'confirmed';
$stmt->bind_param('si', $confirmed, $booking_id);
$stmt->execute();

header('Location: ../admin.php?success=' . urlencode('Booking dikonfirmasi'));
exit();
