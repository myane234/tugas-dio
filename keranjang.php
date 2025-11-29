<?php
session_start();
include 'koneksi.php';

// Pastikan user login
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['id_user'];

// Tangani penambahan dari index.php (hidden input name="add_id")
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_id'])){
    $motor_id = (int)$_POST['add_id'];

    // Ambil harga motor
    $stmt = $conn->prepare("SELECT harga FROM motor WHERE id = ?");
    $stmt->bind_param("i", $motor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows === 0){
        $stmt->close();
        // Motor tidak ditemukan
        header("Location: index.php");
        exit();
    }
    $row = $res->fetch_assoc();
    $harga_satuan = (int)$row['harga'];
    $stmt->close();

    // Cek apakah sudah ada di checkout (user + motor)
    $stmt = $conn->prepare("SELECT id, jumlah_beli FROM checkout WHERE user_id = ? AND motor_id = ?");
    $stmt->bind_param("ii", $user_id, $motor_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        // Update jumlah_beli jika sudah ada (tambah 1)
        $item = $res->fetch_assoc();
        $new_qty = (int)$item['jumlah_beli'] + 1;
        $new_total = $new_qty * $harga_satuan; // $total = jumlah_beli * harga_satuan
        $update = $conn->prepare("UPDATE checkout SET jumlah_beli = ?, harga_satuan = ?, total_harga = ? WHERE id = ?");
        $update->bind_param("iiii", $new_qty, $harga_satuan, $new_total, $item['id']);
        $update->execute();
        $update->close();
    } else {
        // Insert baris baru
        $qty = 1;
        $total = $qty * $harga_satuan; // $total = jumlah_beli * harga_satuan
        $ins = $conn->prepare("INSERT INTO checkout (user_id, motor_id, jumlah_beli, harga_satuan, total_harga) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("iiiii", $user_id, $motor_id, $qty, $harga_satuan, $total);
        $ins->execute();
        $ins->close();
    }

    // Setelah tambah, redirect ke halaman keranjang untuk menghindari resubmit
    header("Location: keranjang.php");
    exit();
}

// Ambil daftar item keranjang untuk user
$stmt = $conn->prepare("
    SELECT c.id AS checkout_id, c.jumlah_beli, c.harga_satuan, c.total_harga,
           m.id AS motor_id, m.jenis_motor, m.merk_motor, m.gambar
    FROM checkout c
    JOIN motor m ON c.motor_id = m.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Keranjang — Kawasaki Ninja Store</title>
<style>
body{font-family:Arial, sans-serif;background:#000;color:#fff;padding:20px;}
table{width:100%;border-collapse:collapse;margin-top:12px;}
th,td{padding:10px;border-bottom:1px solid #333;text-align:left;}
img{max-width:120px;height:auto;}
.btn{background:#00ff3c;color:#000;padding:8px 12px;border-radius:6px;text-decoration:none;font-weight:700;}
.remove{background:#ff4d4d;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;}
.total{font-size:18px;font-weight:800;color:#00ff3c;margin-top:12px;}
</style>
</head>
<body>

<h2>Keranjang Anda</h2>

<?php if($items->num_rows === 0): ?>
    <p>Keranjang kosong. <a class="btn" href="index.php">Kembali belanja</a></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $grand_total = 0;
            while($it = $items->fetch_assoc()):
                $grand_total += (int)$it['total_harga'];
        ?>
            <tr>
                <td>
                    <img src="<?= htmlspecialchars($it['gambar']) ?>" alt="gambar">
                    <div><?= htmlspecialchars($it['jenis_motor']) ?> — <?= htmlspecialchars($it['merk_motor']) ?></div>
                </td>
                <td>Rp <?= number_format($it['harga_satuan'],0,',','.') ?></td>
                <td><?= (int)$it['jumlah_beli'] ?></td>
                <td>Rp <?= number_format($it['total_harga'],0,',','.') ?></td>
                <td>
                  <a class="remove" href="hapus.php?id=<?= $it['checkout_id'] ?>" onclick="return confirm('Hapus item ini dari keranjang?')">Hapus</a>

                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total">Total Belanja: Rp <?= number_format($grand_total,0,',','.') ?></div>
    <p style="margin-top:12px;">
        <a class="btn" href="index.php">Lanjut Belanja</a>
        <!-- Tambahkan tombol checkout/payment sesuai kebutuhan -->
    </p>
<?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
