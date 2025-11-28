<?php
session_start();
include 'koneksi.php';

// Anti-cache
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$display_name = $_SESSION['username'];

// Ambil produk
$result = $conn->query("SELECT * FROM motor");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kawasaki Ninja Store</title>

<!-- Font Racing -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">

<style>
/* ========== BODY ========== */
body{
    margin:0;
    padding:0;
    font-family:'Orbitron', sans-serif;
    background:linear-gradient(135deg,#000000,#091b00,#000000);
    background-size:200% 200%;
    animation:bgMove 8s infinite alternate;
    color:white;
}
@keyframes bgMove{
    from{background-position:0% 0%;}
    to{background-position:100% 100%;}
}

/* ========== HEADER ========== */
header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 35px;
    background:rgba(0,0,0,0.9);
    border-bottom:3px solid #00ff3c;
    box-shadow:0 0 30px rgba(0,255,60,0.6);
}

header h2{
    margin:0;
    font-size:25px;
    font-weight:700;
    letter-spacing:2px;
    text-transform:uppercase;
    color:#00ff3c;
    text-shadow:0 0 20px #00ff3c;
}

.user-name{
    font-size:14px;
    color:#c8ffd9;
    margin-right:18px;
    text-transform:uppercase;
}

/* ========== LOGOUT ========== */
.logout-btn{
    background:linear-gradient(135deg,#00ff3c,#008f22);
    color:black;
    padding:10px 20px;
    border:none;
    border-radius:30px;
    font-weight:700;
    cursor:pointer;
    transition:0.3s ease;
}
.logout-btn:hover{
    transform:scale(1.1);
    box-shadow:0 0 20px #00ff3c,0 0 40px #00ff3c80;
}

/* ========== BUTTON KERANJANG ========== */
.cart-btn{
    display:inline-block;
    margin:28px;
    padding:14px 30px;
    background:linear-gradient(135deg,#00ff3c,#009c28);
    color:black;
    border-radius:50px;
    text-decoration:none;
    font-weight:800;
    letter-spacing:2px;
    text-transform:uppercase;
    box-shadow:0 0 20px rgba(0,255,60,0.5);
    transition:.3s;
}
.cart-btn:hover{
    transform:scale(1.15);
    box-shadow:0 0 30px #00ff3c,0 0 60px rgba(0,255,60,0.7);
}

/* ========== JUDUL ========== */
h2.page-title{
    text-align:center;
    color:#00ff3c;
    text-shadow:0 0 20px #00ff3c;
    letter-spacing:2px;
}

/* ========== GRID ========== */
.gallery{
    padding:45px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:35px;
}

/* ========== CARD ========== */
.card{
    background:linear-gradient(160deg,#000000,#032200,#000000);
    border-radius:20px;
    text-align:center;
    border:2px solid rgba(0,255,60,0.5);
    box-shadow:0 0 40px rgba(0,0,0,0.9);
    transition:0.35s ease;
    position:relative;
    overflow:hidden;
}
.card::after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(120deg,transparent,rgba(0,255,60,0.15),transparent);
    opacity:0;
    transition:.3s;
}
.card:hover::after{opacity:1;}
.card:hover{
    transform:translateY(-12px) scale(1.03);
    box-shadow:0 0 30px rgba(0,255,60,0.6), 0 0 60px rgba(0,255,60,0.3);
}

/* ========== IMAGE ========== */
.product-img{
    width:100%;
    height:240px;
    object-fit:contain;
    background:black;
    border-bottom:2px solid rgba(0,255,60,0.3);
    transition:.35s ease;
}
.card:hover .product-img{
    transform:scale(1.14) rotate(-1deg);
}

/* ========== TITLE & PRICE ========== */
.title{
    font-size:16px;
    font-weight:800;
    margin:14px 0 5px;
    letter-spacing:1px;
    text-transform:uppercase;
    color:#e8ffee;
}

.price{
    font-size:17px;
    font-weight:800;
    color:#00ff3c;
    text-shadow:0 0 12px #00ff3c;
    margin-bottom:12px;
}

/* ========== BUTTON ADD ========== */
.btn-add{
    margin:10px 0 22px;
    padding:12px 25px;
    border:none;
    border-radius:30px;
    background:linear-gradient(135deg,#00ff3c,#00a524);
    color:black;
    font-weight:800;
    letter-spacing:1px;
    cursor:pointer;
    transition:.3s;
}
.btn-add:hover{
    transform:scale(1.15);
    box-shadow:0 0 20px #00ff3c,0 0 40px rgba(0,255,60,0.5);
}

/* ========== FOOTER ========== */
footer{
    text-align:center;
    padding:20px;
    font-size:13px;
    font-weight:700;
    color:#99ffb3;
    letter-spacing:2px;
    text-transform:uppercase;
}
</style>
</head>
<body>

<header>
    <h2>Kawasaki Ninja Store</h2>
    <div style="display:flex;align-items:center;">
        <div class="user-name">Hai, <?= htmlspecialchars($display_name) ?>!</div>
        <form method="post" action="logout.php">
            <button class="logout-btn">Logout</button>
        </form>
    </div>
</header>

<a href="keranjang.php" class="cart-btn">⚡ Lihat Keranjang</a>

<h2 class="page-title">🏍️ ShowRoom Taufik kontol</h2>

<div class="gallery">
<?php while($row = $result->fetch_assoc()): ?>
    <div class="card">
        <img src="<?= htmlspecialchars($row['gambar']) ?>" class="product-img" alt="motor">

        <h3 class="title"><?= htmlspecialchars($row['jenis_motor']) ?></h3>
        <h4 class="title"><?= htmlspecialchars($row['merk_motor']) ?></h4>
        <p class="price">Rp <?= number_format($row['harga'],0,',','.') ?></p>

        <form method="POST" action="keranjang.php">
            <input type="hidden" name="add_id" value="<?= $row['id'] ?>">
            <button class="btn-add">+ Tambah ke Keranjang</button>
        </form>
    </div>
<?php endwhile; ?>
</div>

<footer>© <?= date('Y') ?> Kawasaki Ninja Store — <b>DIO SEPRILIAN PUTRA</b></footer>

</body>
</html>
