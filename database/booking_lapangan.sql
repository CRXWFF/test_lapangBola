## 🗄️ Database Schema

### Tabel `users`
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_telepon VARCHAR(15),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel `lapangan`
```sql
CREATE TABLE lapangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lapangan VARCHAR(100) NOT NULL,
    jenis VARCHAR(50),
    harga_per_jam INT NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel `booking`
```sql
CREATE TABLE booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    lapangan_id INT NOT NULL,
    tanggal_booking DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    total_harga INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (lapangan_id) REFERENCES lapangan(id)
);
```

### Data Dummy Lapangan
```sql
INSERT INTO lapangan (nama_lapangan, jenis, harga_per_jam) VALUES
('Lapangan A', 'Futsal', 100000),
('Lapangan B', 'Futsal', 120000),
('Lapangan C', 'Mini Soccer', 150000);
```

### Data Dummy Admin
```sql
INSERT INTO users (nama, email, password, role) VALUES
('Admin', 'admin@lapangan.com', '$2y$10$encrypted_password', 'admin');
-- Password: admin123 (harus di-hash dengan password_hash())
```