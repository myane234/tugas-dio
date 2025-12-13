<?php
include '../koneksi.php';

// Handle form submission untuk create motor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_motor'])) {
    $jenis_motor = mysqli_real_escape_string($conn, $_POST['jenis_motor']);
    $merk_motor = mysqli_real_escape_string($conn, $_POST['merk_motor']);
    $harga = (int)$_POST['harga'];
    $jumlah = (int)$_POST['jumlah'];
    
    // Handle upload gambar
    $gambar_path = NULL;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES['gambar']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validasi: hanya gambar (jpg, png, jpeg, gif)
        if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar_path = "images/" . basename($_FILES['gambar']['name']);
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Only image files are allowed.";
        }
    }
    
    // Insert ke database
    if ($jenis_motor && $merk_motor && $harga && $jumlah) {
        $query = "INSERT INTO motor (jenis_motor, merk_motor, harga, jumlah, gambar) VALUES ('$jenis_motor', '$merk_motor', $harga, $jumlah, '$gambar_path')";
        if (mysqli_query($conn, $query)) {
            header("Location: dashboard.php"); // Redirect setelah insert
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

$query = "SELECT * FROM motor";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/create.css">
</head>
<body>
    <div class="navbar">
        <h1>dashboard Admin</h1>
        <section><button><a href='../logout.php'>Log Out</a></button></section>
    </div>
    
    <!-- Form untuk create motor -->
    <div class="createForm">
        <h2>Tambah Motor Baru</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="jenis_motor">Jenis Motor:</label>
            <input type="text" name="jenis_motor" required><br>
            
            <label for="merk_motor">Merk Motor:</label>
            <input type="text" name="merk_motor" required><br>
            
            <label for="harga">Harga:</label>
            <input type="number" name="harga" required><br>
            
            <label for="jumlah">Jumlah:</label>
            <input type="number" name="jumlah" required><br>
            
            <label for="gambar">Gambar:</label>
            <input type="file" name="gambar" accept="image/*"><br>
            
            <button type="submit" name="create_motor">Tambah Motor</button>
        </form>
    </div>
    
    </div>
</body>
</html>