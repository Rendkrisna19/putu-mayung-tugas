<?php
session_start();
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_product = $_POST['id_product'];
    $nama_product = $_POST['nama_product'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $rasa = $_POST['rasa'];
    $gambar = $_FILES['gambar'];

    // Validasi data
    if (empty($nama_product) || empty($deskripsi) || empty($harga) || empty($stok) || empty($rasa)) {
        $_SESSION['error'] = "Semua field harus diisi.";
        header("Location: list_product.php");
        exit();
    }

    // Handle file upload jika ada gambar baru
    $gambar_name = "";
    if ($gambar['size'] > 0) {
        $target_dir = "../../upload/";
        $target_file = $target_dir . basename($gambar["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($gambar["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $_SESSION['error'] = "File bukan gambar.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $_SESSION['error'] = "File sudah ada.";
            $uploadOk = 0;
        }

        // Check file size
        if ($gambar["size"] > 500000) {
            $_SESSION['error'] = "Ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $_SESSION['error'] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            header("Location: list_product.php");
            exit();
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($gambar["tmp_name"], $target_file)) {
                $gambar_name = basename($gambar["name"]);
            } else {
                $_SESSION['error'] = "Terjadi kesalahan saat mengunggah file.";
                header("Location: list_product.php");
                exit();
            }
        }
    }

    // Update data produk di database
    if (!empty($gambar_name)) {
        $stmt = $conn->prepare("UPDATE products SET nama_product = ?, deskripsi = ?, harga = ?, stok = ?, rasa = ?, gambar = ? WHERE id_product = ?");
        $stmt->bind_param("ssdisii", $nama_product, $deskripsi, $harga, $stok, $rasa, $gambar_name, $id_product);
    } else {
        $stmt = $conn->prepare("UPDATE products SET nama_product = ?, deskripsi = ?, harga = ?, stok = ?, rasa = ? WHERE id_product = ?");
        $stmt->bind_param("ssdisi", $nama_product, $deskripsi, $harga, $stok, $rasa, $id_product);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Produk berhasil diupdate.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat mengupdate produk.";
    }

    $stmt->close();
    header("Location: list_product.php");
    exit();
}
?>