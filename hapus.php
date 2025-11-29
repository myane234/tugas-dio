<?php
session_start();
include 'koneksi.php';

// Cek login
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

// Pastikan ada id yang dikirim
if (!isset($_GET['id'])) {
    header("Location: keranjang.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$delete_id = intval($_GET['id']);

// Cek apakah item benar milik user
$cek = $conn->prepare("SELECT id FROM checkout WHERE id = ? AND user_id = ?");
$cek->bind_param("ii", $delete_id, $id_user);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows > 0) {
    // Hapus jika milik user
    $hapus = $conn->prepare("DELETE FROM checkout WHERE id = ?");
    $hapus->bind_param("i", $delete_id);
    $hapus->execute();
}

// Balikin ke keranjang
header("Location: keranjang.php");
exit();
