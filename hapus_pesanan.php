<?php
include 'koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM pesanan WHERE id='$id'");

header("Location: admin/dashboard.php");
exit;
?>
