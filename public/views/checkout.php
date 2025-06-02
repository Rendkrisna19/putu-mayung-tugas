<?php
session_start();
include("../../config/config.php");


// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// === AJAX: Update jumlah produk di cart ===
if (isset($_POST['ajax']) && $_POST['ajax'] === 'update_cart') {
    $index = intval($_POST['index']);
    $action = $_POST['action']; // 'plus' atau 'minus'

    if (!isset($_SESSION['cart'][$index])) {
        echo json_encode(['success' => false, 'msg' => 'Produk tidak ditemukan di keranjang.']);
        exit;
    }

    $item = &$_SESSION['cart'][$index];
    $id_product = $item['id_product'];

    // Ambil stok terbaru
    $stmt = $conn->prepare("SELECT stok FROM products WHERE id_product = ?");
    $stmt->bind_param("i", $id_product);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stok = $product['stok'];

    if ($action === 'plus') {
        if ($stok < 1) {
            echo json_encode(['success' => false, 'msg' => 'Stok tidak mencukupi.']);
            exit;
        }
        $item['jumlah'] += 1;
        $item['total'] = $item['jumlah'] * $item['harga'];
        // Kurangi stok di database
        $stmt = $conn->prepare("UPDATE products SET stok = stok - 1 WHERE id_product = ?");
        $stmt->bind_param("i", $id_product);
        $stmt->execute();
    } elseif ($action === 'minus') {
        if ($item['jumlah'] <= 1) {
            echo json_encode(['success' => false, 'msg' => 'Minimal 1 produk.']);
            exit;
        }
        $item['jumlah'] -= 1;
        $item['total'] = $item['jumlah'] * $item['harga'];
        // Tambah stok di database
        $stmt = $conn->prepare("UPDATE products SET stok = stok + 1 WHERE id_product = ?");
        $stmt->bind_param("i", $id_product);
        $stmt->execute();
    }

    // Hitung subtotal baru
    $subtotal = array_sum(array_column($_SESSION['cart'], 'total'));
    echo json_encode([
        'success' => true,
        'jumlah' => $item['jumlah'],
        'total' => number_format($item['total'], 0, ',', '.'),
        'subtotal' => number_format($subtotal, 0, ',', '.')
    ]);
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
    $user_id = $_SESSION['user_id'];
    $total_harga = array_sum(array_column($_SESSION['cart'], 'total')) + $ongkir;

    $stmt = $conn->prepare("INSERT INTO orders (user_id, alamat, phone, ongkir, total_harga) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $alamat, $phone, $ongkir, $total_harga);
    $stmt->execute();
    $id_order = $stmt->insert_id;
    $_SESSION['id_order'] = $id_order;

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
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e0e7ff 0%, #f3f4f6 100%);
    }

    .btn-qty {
        transition: background 0.2s, color 0.2s;
    }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-100 to-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto my-10 p-8 bg-white shadow-2xl rounded-3xl border border-indigo-100">
        <h1 class="text-3xl font-extrabold mb-8 text-indigo-600 tracking-tight">Checkout & Keranjang Belanja</h1>

        <?php if (!empty($_SESSION['cart'])): ?>
        <!-- Keranjang -->
        <h2 class="text-xl font-semibold mb-4 text-indigo-700">Keranjang</h2>
        <div class="overflow-x-auto rounded-xl shadow">
            <table class="min-w-full mb-6 border rounded-xl overflow-hidden bg-white">
                <thead class="bg-indigo-50 text-sm text-indigo-700 uppercase">
                    <tr>
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <tr class="text-base text-gray-700 border-b hover:bg-indigo-50 transition">
                        <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($item['nama_product']) ?></td>
                        <td class="px-6 py-4">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button"
                                    class="btn-qty bg-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-lg shadow"
                                    onclick="updateQty(<?= $index ?>, 'minus')">-</button>
                                <span id="qty-<?= $index ?>"
                                    class="mx-2 min-w-[2rem] text-center"><?= $item['jumlah'] ?></span>
                                <button type="button"
                                    class="btn-qty bg-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-lg shadow"
                                    onclick="updateQty(<?= $index ?>, 'plus')">+</button>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            Rp <span id="total-<?= $index ?>"><?= number_format($item['total'], 0, ',', '.') ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <form action="hapus_cart.php" method="POST" onsubmit="return confirm('Hapus produk ini?');">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button class="text-red-500 hover:underline font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="font-bold bg-indigo-50 text-indigo-700">
                        <td colspan="3" class="px-6 py-4 text-right">Subtotal</td>
                        <td class="px-6 py-4" colspan="2">Rp
                            <span
                                id="subtotal"><?= number_format(array_sum(array_column($_SESSION['cart'], 'total')), 0, ',', '.') ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form Checkout -->
        <h2 class="text-xl font-semibold mt-10 mb-4 text-indigo-700">Form Checkout</h2>
        <form action="checkout.php" method="POST" class="space-y-6">
            <div>
                <label for="phone" class="block text-gray-700 font-medium">Nomor Telepon</label>
                <input type="text" id="phone" name="phone"
                    class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-400 outline-none"
                    required>
            </div>
            <div>
                <label for="alamat" class="block text-gray-700 font-medium">Alamat Pengiriman</label>
                <textarea id="alamat" name="alamat"
                    class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-400 outline-none"
                    required></textarea>
            </div>
            <div>
                <label for="ongkir" class="block text-gray-700 font-medium">Pilih Ongkir</label>
                <select name="ongkir" id="ongkir"
                    class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-400 outline-none"
                    required>
                    <?php foreach ($ongkir_list as $ong): ?>
                    <option value="<?= $ong['tarif'] ?>">
                        <?= htmlspecialchars($ong['daerah']) ?> - Rp <?= number_format($ong['tarif'], 0, ',', '.') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold text-lg shadow-lg hover:bg-indigo-700 transition">
                Konfirmasi & Checkout
            </button>
        </form>

        <?php else: ?>
        <p class="text-gray-600 text-lg">Keranjang belanja kosong.</p>
        <?php endif; ?>

        <div class="mt-10">
            <a href="index.php" class="text-indigo-600 hover:underline font-semibold text-lg">← Kembali ke Toko</a>
        </div>
    </div>

    <script>
    function updateQty(index, action) {
        fetch('checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'ajax=update_cart&index=' + index + '&action=' + action
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('qty-' + index).innerText = data.jumlah;
                    document.getElementById('total-' + index).innerText = data.total;
                    document.getElementById('subtotal').innerText = data.subtotal;
                } else {
                    alert(data.msg);
                }
            });
    }
    </script>
    <?php  
        include ("../../components/Footer.php");

    ?>
</body>

</html>