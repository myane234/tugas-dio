<?php
session_start();
include '../koneksi.php';


$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if(empty($nama) || empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif(strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', $nama)) {
        $error = "Username hanya boleh huruf, angka, dan underscore!";
    } else {
        // Cek username sudah ada
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $nama);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password yang aman
            $hashPw = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert dengan prepared statement
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO users (username, password, role, created_at) VALUES (?, ?, 'admin', NOW())");
            mysqli_stmt_bind_param($stmt, "ss", $nama, $hashPw);
            
            if(mysqli_stmt_execute($stmt)) {
                $success = "Admin berhasil dibuat!";
                // Reset form
                $_POST = array();
            } else {
                $error = "Gagal membuat admin: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <link rel="stylesheet" href="./css/registerAdmin.css">
    <style>
        .error { color: red; margin: 10px 0; }
        .success { color: green; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register Admin Baru</h1>
        
        <?php if($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="nama">Username: *</label>
                <input type="text" id="nama" name="nama" 
                       value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password: *</label>
                <input type="password" id="password" name="password" required>
                <button type="button" onclick="togglePassword('password')">Show</button>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password: *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="button" onclick="togglePassword('confirm_password')">Show</button>
            </div>
            
            <div class="form-group">
                <button type="submit">Buat Admin</button>
            </div>
        </form>
        
        <div class="links">
            <a href="../dashboard.php">Dashboard</a> | 
            <a href="list_admin.php">Lihat Daftar Admin</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            
            if(input.type === 'password') {
                input.type = 'text';
                button.textContent = 'Hide';
            } else {
                input.type = 'password';
                button.textContent = 'Show';
            }
        }
        
        // Validasi form client-side
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if(password !== confirm) {
                alert('Password tidak cocok!');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>