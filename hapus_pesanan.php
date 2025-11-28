<?php
include 'koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM pesanan WHERE id='$id'");

header("Location: dashboard_admin.php");
exit;
?>
