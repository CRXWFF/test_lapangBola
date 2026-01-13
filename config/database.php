<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'booking_lapangan';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    exit('Koneksi ke database gagal. Silakan coba lagi nanti.');
}
