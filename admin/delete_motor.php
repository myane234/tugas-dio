<?php
session_start();
include '../koneksi.php';

// proteksi: hanya admin yang bisa delete
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    // hapus data dari database
    $query = "DELETE FROM motor WHERE id = '$id'";
    mysqli_query($conn, $query);
}

// balik ke dashboard
header("Location: dashboard.php");
exit;

?>