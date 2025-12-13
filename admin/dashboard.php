<?php
include '../koneksi.php';

$query = "SELECT * FROM motor";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="navbar">
        <h1>dashboard Admin</h1>
        <a class="create" href="create.php">+ Tambah Data</a>

        

        <section><button><a href='../logout.php'>Log Out</a></button></section>
        
    </div>
    

    <div class="dataTable">
           <table border="1">
        <thead>
            <tr>
            <th>ID</th>
            <th>Jenis Motor</th>
            <th>Merk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Gambar</th>
            <th>Delete</th>
            <th>Update</th>
            </tr>
        </thead>
        
   <?php
if(mysqli_num_rows($result) > 0) {
    $no = 1;
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>". $no++ ."</td>";
        echo "<td>". $row["jenis_motor"] ."</td>";
        echo "<td>". $row["merk_motor"] ."</td>";
        echo "<td>Rp " . number_format($row["harga"], 0, ',', '.') . "</td>";
        echo "<td>". $row["jumlah"] ."</td>";
        
        // PERBAIKAN DISINI: Tampilkan gambar, bukan teks
        if(!empty($row["gambar"])) {
            echo "<td><img src='../" . $row["gambar"] . "' width='100' height='50'></td>";
        } else {
            echo "<td>Tidak ada gambar</td>";
        }
        
        echo "<td>
                <form action='delete_motor.php' method='post' onsubmit=\"return confirm('Yakin hapus data ini?')\">
                <input type='hidden' name='id' value='". $row["id"] ."'>
                <button type='submit' class='btn-delete'>Delete</button>
            </form>
        </td>";

        // tombol Update
        echo "<td>
                <a href='edit_motor.php?id=". $row["id"] ."' class='btn-update'>Update</a>
              </td>";
        echo "</tr>";
    }
} else {
    // PERBAIKAN: colspan harus 7 karena ada 7 kolom (No, Jenis, Merk, Harga, Jumlah, Gambar, Aksi)
    echo "<tr><td colspan='7'>Tidak ada data</td></tr>";
}
?>
    </table>
    </div>
 
</body>
</html>