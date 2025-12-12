<?php
include "../koneksi.php";

$token = $_GET['token'];

// cek token
$q = mysqli_query($conn, 
    "SELECT * FROM registration_requests 
     WHERE token='$token' AND status='pending'");

if(mysqli_num_rows($q) == 0){
    die("Token tidak valid / sudah dipakai");
}

$data = mysqli_fetch_assoc($q);
$user_id = $data['user_id'];

// approve user
mysqli_query($conn, "UPDATE users SET status='approved' WHERE id=$user_id");

// ubah status request
mysqli_query($conn, "UPDATE registration_requests SET status='approved' WHERE token='$token'");

echo "User berhasil di-approve!";
?>
