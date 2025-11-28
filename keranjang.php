<?php
session_start();
include 'koneksi.php';

// Pastikan user login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$display_name = $_SESSION['username'];

// Inisialisasi keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Tambah barang
if (isset($_POST['add_id'])) {
    $id = $_POST['add_id'];
    $q = $conn->query("SELECT * FROM photo WHERE id='$id'");
    $data = $q->fetch_assoc();
    if(!isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id] = [
            'nama'=>$data['nama'],
            'harga'=>(int)$data['harga'],
            'foto'=>$data['foto'],
            'qty'=>1
        ];
    } else {
        $_SESSION['cart'][$id]['qty']++;
    }
    header("Location:keranjang.php"); 
    exit();
}

// Update qty
if (isset($_POST['update_cart'])) {
    foreach($_POST['qty'] as $id=>$jumlah){
        $_SESSION['cart'][$id]['qty'] = max(1,(int)$jumlah);
    }
    $updated = true;
}

// Hapus item
if(isset($_GET['hapus'])){
    unset($_SESSION['cart'][$_GET['hapus']]);
    header("Location:keranjang.php"); 
    exit();
}

// Checkout
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $id_user = $_SESSION['id_user'];
    foreach($_SESSION['cart'] as $id_barang=>$item){
        $jumlah = $item['qty'];
        $total = $item['harga']*$jumlah;
        $conn->query("INSERT INTO keranjang(id_user,id_barang,jumlah,total_harga) VALUES('$id_user','$id_barang','$jumlah','$total')");
    }
    $_SESSION['cart'] = [];
    header("Location:keranjang.php?checkout_success=1"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keranjang - Kawasaki Ninja</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
  background:radial-gradient(circle at top,#0b2412,#000);
  color:#E0FFE7;
  padding:20px;
}

/* Judul */
h2{
  text-align:center;
  font-family:'Orbitron',sans-serif;
  font-size:32px;
  color:#39FF14;
  text-shadow:0 0 15px #39FF14;
  margin-bottom:25px;
  letter-spacing:2px;
}

/* BOX */
.cart-box{
  background:rgba(0,20,5,0.9);
  border:1px solid rgba(57,255,20,0.2);
  box-shadow:0 0 35px rgba(57,255,20,0.2);
  border-radius:20px;
  padding:20px;
  max-width:1000px;
  margin:auto;
}

/* TABLE */
table{width:100%;border-collapse:collapse;}
th,td{
  padding:12px;
  border-bottom:1px solid rgba(255,255,255,0.08);
  text-align:center;
}
th{
  background:rgba(57,255,20,0.1);
  color:#39FF14;
  font-family:'Orbitron',sans-serif;
  text-transform:uppercase;
  font-size:13px;
  letter-spacing:1px;
}

/* GAMBAR */
td img{
  width:80px;height:80px;object-fit:cover;
  border-radius:14px;
  border:2px solid #39FF14;
  box-shadow:0 0 12px rgba(57,255,20,0.5);
  transition:.25s;
}
td img:hover{
  transform:scale(1.1) rotate(-2deg);
}

/* INPUT */
input[type=number]{
  width:70px;
  padding:6px;
  border-radius:8px;
  background:#000;
  border:1px solid #39FF14;
  color:#39FF14;
  text-align:center;
  font-weight:600;
}

/* BUTTON BASE */
.btn{
  padding:7px 15px;
  border-radius:8px;
  font-weight:600;
  border:none;
  cursor:pointer;
  transition:.3s;
}

/* Buttons */
.update-btn{
  background:linear-gradient(to right,#1aff00,#39FF14);
  color:#003300;
  box-shadow:0 0 10px rgba(57,255,20,0.8);
}
.update-btn:hover{transform:scale(1.08);}

.delete-btn{
  background:#ff0033;
  color:white;
  box-shadow:0 0 10px rgba(255,0,50,0.6);
}
.delete-btn:hover{transform:scale(1.08);}

.checkout-btn{
  background:linear-gradient(to right,#39FF14,#00ff88);
  color:#002200;
  padding:12px 20px;
  font-weight:700;
  border-radius:12px;
  text-decoration:none;
  box-shadow:0 0 15px rgba(57,255,20,0.9);
}
.checkout-btn:hover{
  transform:scale(1.1);
  box-shadow:0 0 25px #39FF14;
}

/* TOTAL */
.total-box{
  text-align:right;
  font-size:20px;
  font-family:'Orbitron',sans-serif;
  margin-top:15px;
  color:#39FF14;
  text-shadow:0 0 10px #39FF14;
}

/* MESSAGE */
.success-msg{
  background:linear-gradient(to right,#00ff88,#39FF14);
  color:#002200;
  padding:12px;
  border-radius:12px;
  text-align:center;
  font-weight:700;
  margin-bottom:15px;
  box-shadow:0 0 20px rgba(57,255,20,0.6);
}

/* BACK BTN */
.back-btn{
  display:inline-block;
  margin-top:20px;
  padding:12px 18px;
  border-radius:12px;
  text-decoration:none;
  background:#000;
  color:#39FF14;
  border:1px solid #39FF14;
  font-weight:600;
  box-shadow:0 0 12px rgba(57,255,20,0.5);
  transition:.3s;
}
.back-btn:hover{
  transform:translateX(-8px);
  box-shadow:0 0 25px #39FF14;
}
</style>

</head>
<body>

<h2>🏍️ KERANJANG KAWASAKI NINJA</h2>

<div class="cart-box">

<?php if(isset($updated)): ?>
<div class="success-msg">✅ Keranjang berhasil diperbarui!</div>
<?php endif; ?>

<?php if(isset($_GET['checkout_success'])): ?>
<div class="success-msg">🎉 Checkout berhasil!</div>
<?php endif; ?>

<form method="POST">
<table>
<tr>
  <th>No</th>
  <th>Foto</th>
  <th>Nama</th>
  <th>Harga</th>
  <th>Jumlah</th>
  <th>Subtotal</th>
  <th>Aksi</th>
</tr>

<?php
$total = 0; $no=1;
if(!empty($_SESSION['cart'])):
foreach($_SESSION['cart'] as $id=>$item):
$subtotal = $item['harga']*$item['qty'];
$total += $subtotal;
?>
<tr>
  <td><?= $no++ ?></td>
  <td><img src="images/<?= $item['foto'] ?>"></td>
  <td><?= htmlspecialchars($item['nama']) ?></td>
  <td>Rp <?= number_format($item['harga'],0,',','.') ?></td>
  <td><input type="number" name="qty[<?= $id ?>]" value="<?= $item['qty'] ?>" min="1"></td>
  <td>Rp <?= number_format($subtotal,0,',','.') ?></td>
  <td>
    <a href="?hapus=<?= $id ?>" onclick="return confirm('Hapus item?')">
      <button type="button" class="btn delete-btn">Hapus</button>
    </a>
  </td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="7" style="color:#ff5555;">Keranjang kosong!</td></tr>
<?php endif; ?>
</table>

<?php if(!empty($_SESSION['cart'])): ?>
<br>
<button type="submit" name="update_cart" class="btn update-btn">Update Keranjang</button>
<button type="submit" name="checkout" class="checkout-btn">Checkout</button>
<?php endif; ?>
</form>

<div class="total-box">
Total Bayar: Rp <?= number_format($total,0,',','.') ?>
</div>

</div>

<a href="index.php" class="back-btn">⬅ Kembali Belanja</a>

</body>
</html>
