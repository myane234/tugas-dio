<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // cek apakah username sudah ada
    $sql_check = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql_check);

    if ($result && $result->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql)) {
            header("Location: login.php?success=1");
            exit;
        } else {
            $error = "Gagal mendaftar: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register - Canva App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #74ABE2, #5563DE);
  font-family: 'Poppins', sans-serif;
  color: #fff;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card {
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  padding: 25px;
  width: 350px;
}
</style>
</head>
<body>
<div class="card">
  <h4 class="text-center mb-3">Buat Akun</h4>
  <?php if(isset($error)): ?>
    <div class="alert alert-danger p-2"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-light w-100" type="submit">Daftar</button>
    <p class="text-center mt-2 mb-0">
      Sudah punya akun? <a href="login.php" class="text-warning">Login di sini</a>
    </p>
  </form>
</div>
</body>
</html>