<?php
include "koneksi.php";

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

// reject user
mysqli_query($conn, "UPDATE users SET status='rejected' WHERE id=$user_id");

// update token
mysqli_query($conn, "UPDATE registration_requests SET status='rejected' WHERE token='$token'");

echo "User berhasil ditolak.";
?>
