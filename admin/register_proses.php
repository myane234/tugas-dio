<?php
include "koneksi.php";

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// 1. Buat user dengan status "pending"
mysqli_query($conn, "INSERT INTO users (username, password, status, role) 
                     VALUES ('$username', '$password', 'pending', 'user')");

$user_id = mysqli_insert_id($conn);

// 2. Generate token random
$token = bin2hex(random_bytes(32));
$expired_at = date("Y-m-d H:i:s", time() + 3600 * 24); // 24 jam

mysqli_query($conn, "INSERT INTO registration_requests (user_id, token, expired_at) 
                     VALUES ($user_id, '$token', '$expired_at')");

// 3. Kirim email ke owner
$ownerEmail = "flytothemoonkawaii@gmail.com";

$approveLink = "http://localhost/approve.php?token=$token";
$rejectLink  = "http://localhost/reject.php?token=$token";

$subject = "Approval User Baru";
$message = "
Ada user baru mendaftar:\n
Username: $username\n\n
Approve: $approveLink\n
Reject : $rejectLink
";

$headers = "From: noreply@system.com";

mail($ownerEmail, $subject, $message, $headers);

echo "Registrasi berhasil! Menunggu persetujuan admin.";
?>
