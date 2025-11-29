<?php
// ===============================
// KONFIGURASI KONEKSI
// ===============================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "kawasaki";

// Koneksi awal tanpa DB
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Gagal koneksi: " . $conn->connect_error);
}
echo "Koneksi OK<br>";

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql)) {
    echo "Database '$dbname' OK<br>";
}

$conn->select_db($dbname);


$sql_users = "
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql_users)) {
    echo "Table users OK<br>";
}

$sql_motor = "
CREATE TABLE IF NOT EXISTS motor (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    jenis_motor VARCHAR(100) NOT NULL,
    merk_motor VARCHAR(100) NOT NULL,
    harga INT(11) NOT NULL,
    jumlah INT(11) NOT NULL,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql_motor)) {
    echo "Table motor OK<br>";
}


$sql_checkout = "
CREATE TABLE IF NOT EXISTS checkout (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    motor_id INT(11) NOT NULL,
    jumlah_beli INT(11) NOT NULL,
    harga_satuan INT(11) NOT NULL,
    total_harga INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY user_id (user_id),
    KEY motor_id (motor_id)
)";
if ($conn->query($sql_checkout)) {
    echo "Table checkout OK<br>";
}

// ===============================
// INSERT SEED DATA USERS
// ===============================
$cek_user = $conn->query("SELECT * FROM users WHERE id = 1");

if ($cek_user->num_rows == 0) {
    $conn->query("
        INSERT INTO users (id, username, password, role) VALUES
        (1, 'faruq', '202cb962ac59075b964b07152d234b70', 'user')
    ");
    echo "Seed users OK<br>";
} else {
    echo "Seed users sudah ada<br>";
}


$cek_motor = $conn->query("SELECT COUNT(*) AS cnt FROM motor")->fetch_assoc()['cnt'];

if ($cek_motor == 0) {
    $conn->query("
        INSERT INTO motor (id, jenis_motor, merk_motor, harga, jumlah, gambar) VALUES
        (4, 'h2r',  'kawasaki ninja', 200000, 10, 'images/h2r.jpeg'),
        (5, 'zx6r', 'kawasaki ninja',  30000, 20, 'images/zx6r.jpeg'),
        (6, 'zx25r','kawasaki ninja', 450000, 20, 'images/zx25r.jpeg'),
        (7, 'zx10r','kawasaki ninja', 670000, 21, 'images/zx10r.jpeg')
    ");
    echo "Seed motor OK<br>";
} else {
    echo "Seed motor sudah ada<br>";
}

echo "<br><b>SETUP SELESAI!</b>";
