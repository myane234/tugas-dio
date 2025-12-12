<?php
session_start();
include 'koneksi.php';

// Jika sudah login, redirect
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';
$success = '';

// =========================
// PROSES LOGIN
// =========================
if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $q = $conn->query("SELECT * FROM users WHERE username='$username' AND password=MD5('$password')");
    if($q->num_rows > 0){
        $user = $q->fetch_assoc();
        $_SESSION['id_user']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if($user['role'] == 'admin'){
            header("Location: dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "❌ Username atau password salah!";
    }
}

// =========================
// PROSES REGISTER
// =========================
if(isset($_POST['register'])){
    $reg_username = trim($_POST['reg_username']);
    $reg_password = trim($_POST['reg_password']);

    // Cek apakah username sudah ada
    $cek = $conn->query("SELECT * FROM users WHERE username='$reg_username'");
    if($cek->num_rows > 0){
        $error = "❌ Username sudah digunakan!";
    } else {
        // Simpan user baru
        $conn->query("INSERT INTO users (username,password,role) 
                      VALUES('$reg_username', MD5('$reg_password'), 'user')");
        $success = "✅ Akun berhasil dibuat, silakan login!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - Kawasaki Ninja Theme</title>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:radial-gradient(circle at top, #001a00, #000000);
    color:#00ff88;
}

/* Neon background animation */
body::before{
    content:"";
    position:fixed;
    inset:0;
    background:
        repeating-linear-gradient(
            0deg,
            rgba(0,255,136,0.05) 0px,
            rgba(0,255,136,0.05) 1px,
            transparent 1px,
            transparent 10px
        );
    z-index:0;
}

/* Box Login */
.login-box{
    position:relative;
    z-index:1;
    width:360px;
    background:rgba(0,0,0,0.85);
    border-radius:25px;
    padding:35px 30px;
    border:2px solid #00ff88;
    box-shadow:0 0 35px rgba(0,255,136,0.5),
               inset 0 0 25px rgba(0,255,136,0.1);
    text-align:center;
}

/* Title */
.login-box h2{
    font-size:30px;
    letter-spacing:3px;
    margin-bottom:15px;
    text-transform:uppercase;
    text-shadow:0 0 15px #00ff88;
}

/* Kawasaki line */
.kawasaki-line{
    height:3px;
    width:80px;
    background:#00ff88;
    margin:0 auto 20px;
    box-shadow:0 0 20px #00ff88;
}

/* Input */
input{
    width:100%;
    padding:14px;
    margin:10px 0;
    border-radius:12px;
    border:1px solid #00ff88;
    background:#020c02;
    color:#00ff88;
    font-size:14px;
    outline:none;
    transition:0.3s ease;
}

input:focus{
    box-shadow:0 0 15px #00ff88;
}

/* Button */
button{
    width:100%;
    margin-top:15px;
    padding:14px;
    border:none;
    border-radius:30px;
    background:linear-gradient(135deg,#00ff88,#00cc55);
    color:black;
    font-weight:700;
    font-size:16px;
    cursor:pointer;
    transition:0.25s ease;
    box-shadow:0 0 25px rgba(0,255,136,0.6);
}

button:hover{
    transform:scale(1.05);
    box-shadow:0 0 40px rgba(0,255,136,0.9);
}

/* Switch */
.switch{
    color:#00ff88;
    font-size:14px;
    margin-top:18px;
    cursor:pointer;
    text-decoration:underline;
    transition:0.3s;
}
.switch:hover{
    letter-spacing:1px;
}

/* Messages */
.error{
    background:#330000;
    border:1px solid red;
    padding:10px;
    border-radius:12px;
    color:#ff4444;
    margin-bottom:10px;
    font-size:14px;
}

.success{
    background:#002211;
    border:1px solid #00ff88;
    padding:10px;
    border-radius:12px;
    color:#00ff88;
    margin-bottom:10px;
    font-size:14px;
}

a{
    text-decoration: none;
    color: green;
}
</style>

<script>
function showRegister(){
    document.getElementById('login-form').style.display='none';
    document.getElementById('register-form').style.display='block';
}
function showLogin(){
    document.getElementById('login-form').style.display='block';
    document.getElementById('register-form').style.display='none';
}
</script>

</head>
<body>

<div class="login-box">

<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($success) echo "<div class='success'>$success</div>"; ?>

<!-- LOGIN FORM -->
<div id="login-form">
    <h2>Kawasaki Login User</h2>
    <div class="kawasaki-line"></div>

    <form method="POST">
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <input type="password" name="password" placeholder="Masukkan Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <div class="switch" onclick="showRegister()">
        Belum punya akun? Daftar Sekarang
    </div>

    <div class="switch">
        <a href="admin/registerAdmin.php">kamu admin?</a>
    </div>
</div>

<!-- REGISTER FORM -->
<div id="register-form" style="display:none;">
    <h2>Register User</h2>
    <div class="kawasaki-line"></div>

    <form method="POST">
        <input type="text" name="reg_username" placeholder="Username baru" required>
        <input type="password" name="reg_password" placeholder="Password baru" required>
        <button type="submit" name="register">Daftar</button>
    </form>

    <div class="switch" onclick="showLogin()">
        Sudah punya akun? Login
    </div>
</div>

</div>

</body>
</html>
