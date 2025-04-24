<?php
session_start();
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_product = $_POST['id_product'];

    // Ambil nama file gambar dari database
    $stmt = $conn->prepare("SELECT gambar FROM products WHERE id_product = ?");
    $stmt->bind_param("i", $id_product);
    $stmt->execute();
    $stmt->bind_result($gambar);
    $stmt->fetch();
    $stmt->close();

    // Hapus data produk dari database
    $stmt = $conn->prepare("DELETE FROM products WHERE id_product = ?");
    $stmt->bind_param("i", $id_product);

    if ($stmt->execute()) {
        // Hapus file gambar dari folder upload jika ada
        if (!empty($gambar) && file_exists("../../upload/" . $gambar)) {
            unlink("../../upload/" . $gambar);
        }
        $_SESSION['success'] = "Produk berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menghapus produk.";
    }

    $stmt->close();
    header("Location: list_product.php");
    exit();
}
?>