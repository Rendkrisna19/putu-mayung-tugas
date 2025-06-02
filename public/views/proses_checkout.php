<?php
session_start();
include("../../config/config.php");

// Setelah login berhasil, pastikan di login:
// $_SESSION['user_id'] = $user_data['id']; // contoh penamaan konsisten user_id

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alamat = $_POST['alamat'];
    $ongkir = (int) $_POST['ongkir'];

    // Ambil data checkout dari session
    if (!isset($_SESSION['checkout'])) {
        die("Tidak ada data checkout.");
    }
    $checkout = $_SESSION['checkout'];
    $totalPembayaran = $checkout['total'] + $ongkir;

    // Simpan data order ke database (contoh sederhana)
    $stmt = $conn->prepare("INSERT INTO orders (id_product, jumlah, total, alamat, ongkir) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisi", $checkout['id_product'], $checkout['jumlah'], $totalPembayaran, $alamat, $ongkir);
    if ($stmt->execute()) {
        // Hapus data checkout dari session setelah order tersimpan
        unset($_SESSION['checkout']);
        echo "Order berhasil! Total pembayaran: Rp " . number_format($totalPembayaran, 0, ',', '.');
        // Redirect atau tampilkan halaman sukses order
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }
}
?>