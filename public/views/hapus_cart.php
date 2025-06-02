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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['index'])) {
    $index = (int) $_POST['index'];
    if (isset($_SESSION['cart'][$index])) {
        // Ambil data item dari cart
        $item = $_SESSION['cart'][$index];
        $id_product = $item['id_product'];
        $jumlah_dihapus = $item['jumlah'];

        // Kembalikan stok produk di database
        // Pertama ambil stok saat ini dari database
        $stmt = $conn->prepare("SELECT stok FROM products WHERE id_product = ?");
        $stmt->bind_param("i", $id_product);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $currentStok = (int) $product['stok'];
            $newStok = $currentStok + $jumlah_dihapus;

            // Update stok di database
            $stmt_update = $conn->prepare("UPDATE products SET stok = ? WHERE id_product = ?");
            $stmt_update->bind_param("ii", $newStok, $id_product);
            $stmt_update->execute();
        }

        // Hapus item dari cart
        unset($_SESSION['cart'][$index]);
        // Reindex array agar index berurutan kembali
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}
header("Location: checkout.php");
exit();
?>