<?php
session_start();
include("../../config/config.php");



// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// === Tambah ke keranjang ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_product']) && isset($_POST['jumlah'])) {
    $id_product = intval($_POST['id_product']);
    $jumlah = intval($_POST['jumlah']);

    $stmt = $conn->prepare("SELECT id_product, nama_product, harga, stok FROM products WHERE id_product = ?");
    $stmt->bind_param("i", $id_product);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product || $jumlah > $product['stok']) {
        die("Produk tidak ditemukan atau stok tidak mencukupi.");
    }

    // Kurangi stok
    $newStok = $product['stok'] - $jumlah;
    $stmt = $conn->prepare("UPDATE products SET stok = ? WHERE id_product = ?");
    $stmt->bind_param("ii", $newStok, $id_product);
    $stmt->execute();

    // Inisialisasi cart
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Tambahkan atau update item di cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id_product'] == $product['id_product']) {
            $item['jumlah'] += $jumlah;
            $item['total'] = $item['jumlah'] * $item['harga'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = [
            'id_product' => $product['id_product'],
            'nama_product' => $product['nama_product'],
            'jumlah' => $jumlah,
            'harga' => $product['harga'],
            'total' => $jumlah * $product['harga']
        ];
    }

    header("Location: checkout.php");
    exit;
}

// === Simpan Pesanan ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['alamat'], $_POST['ongkir'], $_POST['phone']) && !isset($_POST['id_product'])) {
    $alamat = $_POST['alamat'];
    $phone = $_POST['phone'];
    $ongkir = intval($_POST['ongkir']);
    $user_id = $_SESSION['user_id']; // ambil dari session yang sudah konsisten
    $total_harga = array_sum(array_column($_SESSION['cart'], 'total')) + $ongkir;

    // Simpan order dengan user_id
    $stmt = $conn->prepare("INSERT INTO orders (user_id, alamat, phone, ongkir, total_harga) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $alamat, $phone, $ongkir, $total_harga);
    $stmt->execute();
    $id_order = $stmt->insert_id;
    $_SESSION['id_order'] = $id_order;

    // Simpan detail item
    $stmt = $conn->prepare("INSERT INTO order_items (id_order, id_product, jumlah, harga, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $total_item = $item['jumlah'] * $item['harga'];
        $stmt->bind_param("iiiii", $id_order, $item['id_product'], $item['jumlah'], $item['harga'], $total_item);
        $stmt->execute();
    }

    unset($_SESSION['cart']);
    header("Location: success.php");
    exit;
}

// === Ambil daftar ongkir ===
$ongkir_list = [];
$result = $conn->query("SELECT * FROM ongkir ORDER BY daerah ASC");
while ($row = $result->fetch_assoc()) {
    $ongkir_list[] = $row;
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100">

    <div class="max-w-5xl mx-auto my-10 p-6 bg-white shadow rounded">
        <h1 class="text-2xl font-bold mb-6 text-blue-700">Checkout & Keranjang Belanja</h1>

        <?php if (!empty($_SESSION['cart'])): ?>
        <!-- Keranjang -->
        <h2 class="text-xl font-semibold mb-4">Keranjang</h2>
        <table class="min-w-full mb-6 border rounded overflow-hidden">
            <thead class="bg-blue-100 text-sm text-gray-600 uppercase">
                <tr>
                    <th class="px-4 py-2">Produk</th>
                    <th class="px-4 py-2">Harga</th>
                    <th class="px-4 py-2">Jumlah</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <tr class="text-sm text-gray-700">
                    <td class="px-4 py-2"><?= htmlspecialchars($item['nama_product']) ?></td>
                    <td class="px-4 py-2">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td class="px-4 py-2"><?= $item['jumlah'] ?></td>
                    <td class="px-4 py-2">Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                    <td class="px-4 py-2">
                        <form action="hapus_cart.php" method="POST" onsubmit="return confirm('Hapus produk ini?');">
                            <input type="hidden" name="index" value="<?= $index ?>">
                            <button class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="font-bold bg-gray-100">
                    <td colspan="3" class="px-4 py-2 text-right">Subtotal</td>
                    <td class="px-4 py-2" colspan="2">Rp
                        <?= number_format(array_sum(array_column($_SESSION['cart'], 'total')), 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Form Checkout -->
        <h2 class="text-xl font-semibold mt-8 mb-4">Form Checkout</h2>
        <form action="checkout.php" method="POST" class="space-y-4">
            <div>
                <label for="phone" class="block text-gray-700 font-medium">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div>
                <label for="alamat" class="block text-gray-700 font-medium">Alamat Pengiriman</label>
                <textarea id="alamat" name="alamat" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
            <div>
                <label for="ongkir" class="block text-gray-700 font-medium">Pilih Ongkir</label>
                <select name="ongkir" id="ongkir" class="w-full px-3 py-2 border rounded" required>
                    <?php foreach ($ongkir_list as $ong): ?>
                    <option value="<?= $ong['tarif'] ?>">
                        <?= htmlspecialchars($ong['daerah']) ?> - Rp <?= number_format($ong['tarif'], 0, ',', '.') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Konfirmasi & Checkout
            </button>
        </form>

        <?php else: ?>
        <p class="text-gray-600">Keranjang belanja kosong.</p>
        <?php endif; ?>

        <div class="mt-6">
            <a href="index.php" class="text-blue-600 hover:underline">← Kembali ke Toko</a>
        </div>
    </div>

</body>

</html>