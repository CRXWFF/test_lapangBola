<?php
session_start();
require_once __DIR__ . '/../config/database.php';
$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (!$nama || !$email || !$password || !$confirm) {
        header('Location: ../register.php?error=' . urlencode('Semua field wajib diisi'));
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../register.php?error=' . urlencode('Email tidak valid'));
        exit();
    }
    if ($password !== $confirm) {
        header('Location: ../register.php?error=' . urlencode('Password dan konfirmasi tidak cocok'));
        exit();
    }
    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header('Location: ../register.php?error=' . urlencode('Email sudah terdaftar'));
        exit();
    }
    $stmt->close();

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    $stmt = $conn->prepare('INSERT INTO users (nama, email, password, role) VALUES (?,?,?,?)');
    $stmt->bind_param('ssss', $nama, $email, $hashed, $role);
    if ($stmt->execute()) {
        header('Location: ../login.php?success=' . urlencode('Registrasi berhasil. Silakan login.'));
        exit();
    } else {
        header('Location: ../register.php?error=' . urlencode('Gagal membuat akun'));
        exit();
    }
} elseif ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        header('Location: ../login.php?error=' . urlencode('Email dan password wajib diisi'));
        exit();
    }
    $stmt = $conn->prepare('SELECT id, nama, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['nama'];
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header('Location: ../admin.php');
            } else {
                header('Location: ../dashboard.php');
            }
            exit();
        } else {
            header('Location: ../login.php?error=' . urlencode('Email atau password salah'));
            exit();
        }
    } else {
        header('Location: ../login.php?error=' . urlencode('Email atau password salah'));
        exit();
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid action';
}
