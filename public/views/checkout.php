<?php
session_start();
include("../../config/config.php");

// Jika data checkout dikirim via POST (dari modal pembelian)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_product'])) {
    $id_product = $_POST['id_product'];
    $jumlah = (int) $_POST['jumlah'];

    // Ambil data produk berdasarkan id_product
    $stmt = $conn->prepare("SELECT id_product, nama_product, harga, stok FROM products WHERE id_product = ?");
    $stmt->bind_param("i", $id_product);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        die("Produk tidak ditemukan.");
    }

    // Validasi stok
    if ($jumlah > $product['stok']) {
        die("Stok tidak mencukupi.");
    }

    // Kurangi stok sementara (opsional, atau bisa dilakukan saat checkout final)
    $newStok = $product['stok'] - $jumlah;
    $stmt = $conn->prepare("UPDATE products SET stok = ? WHERE id_product = ?");
    $stmt->bind_param("ii", $newStok, $id_product);
    $stmt->execute();

    // Simpan data produk ke cart (session)
    // Jika cart belum ada, buat array baru
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Tambahkan produk ke cart, jika produk sudah ada, tambahkan jumlahnya
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id_product'] == $product['id_product']) {
            $item['jumlah'] += $jumlah;
            $item['total'] = $item['jumlah'] * $item['harga']; // Update total
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
    // Redirect ke halaman checkout (GET) untuk menghindari duplikasi POST
    header("Location: checkout.php");
    exit();
}

// Ambil data ongkir untuk ditampilkan pada dropdown
$ongkir_list = [];
$sql = "SELECT * FROM ongkir ORDER BY daerah ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $ongkir_list[] = $row;
    }
}

// Simpan data ke tabel orders saat checkout final
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['alamat']) && isset($_POST['ongkir'])) {
    $alamat = $_POST['alamat'];
    $ongkir = (int) $_POST['ongkir'];
    $total_harga = 0;

    foreach ($_SESSION['cart'] as $item) {
        $total_harga += $item['total'];
    }

    $total_harga += $ongkir;

    // Insert data ke tabel orders
    $stmt = $conn->prepare("INSERT INTO orders (alamat, ongkir, total_harga) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $alamat, $ongkir, $total_harga);
    $stmt->execute();

    // Dapatkan id_order yang baru saja dimasukkan
    $id_order = $stmt->insert_id;

    // Simpan id_order ke session
    $_SESSION['id_order'] = $id_order;

    // Insert data ke tabel order_items
    $stmt = $conn->prepare("INSERT INTO order_items (id_order, id_product, jumlah, harga, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $total_item = $item['jumlah'] * $item['harga'];
        $stmt->bind_param("iiidd", $id_order, $item['id_product'], $item['jumlah'], $item['harga'], $total_item);
        $stmt->execute();
    }

    // Kosongkan cart setelah checkout
    unset($_SESSION['cart']);

    // Redirect ke halaman sukses atau halaman lain yang diinginkan
    header("Location: success.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout & Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


.font-global {
    font-family: "Poppins", sans-serif;
    font-weight: 400;
    font-style: normal;
}
</style>

<body class="bg-gray-100 font-global">
    <div class="max-w-4xl mx-auto my-10 p-6 bg-white shadow rounded">
        <h1 class="text-2xl font-bold mb-6">Checkout & Keranjang Belanja</h1>

        <!-- Tampilkan Data Cart -->
        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <h2 class="text-xl font-semibold mb-4">Daftar Produk di Keranjang</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($_SESSION['cart'] as $index => $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['nama_product']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp
                            <?php echo number_format($item['harga'],0,',','.'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $item['jumlah']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp
                            <?php echo number_format($item['total'],0,',','.'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="hapus_cart.php" method="POST"
                                onsubmit="return confirm('Hapus produk ini dari keranjang?');">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="mb-4 text-gray-700">Keranjang belanja masih kosong.</p>
        <?php endif; ?>

        <!-- Form Checkout Final -->
        <h2 class="text-xl font-semibold mt-8 mb-4">Konfirmasi Checkout</h2>
        <form action="checkout.php" method="POST">
            <div class="mb-4">
                <label for="alamat" class="block text-gray-700 font-medium">Alamat Pengiriman</label>
                <textarea id="alamat" name="alamat" class="w-full border rounded px-3 py-2" required></textarea>
            </div>
            <div class="mb-4">
                <label for="ongkir" class="block text-gray-700 font-medium">Pilih Ongkos Kirim</label>
                <select id="ongkir" name="ongkir" class="w-full border rounded px-3 py-2" required>
                    <?php if(!empty($ongkir_list)): ?>
                    <?php foreach($ongkir_list as $ongkir): ?>
                    <option value="<?php echo $ongkir['tarif']; ?>">
                        <?php echo htmlspecialchars($ongkir['daerah']); ?>: Rp
                        <?php echo number_format($ongkir['tarif'],0,',','.'); ?>
                    </option>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <option value="">Tidak ada data ongkir</option>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Konfirmasi Checkout
            </button>
        </form>

        <!-- Tombol Lanjut Belanja -->
        <div class="mt-6">
            <a href="index.php"
                class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Lanjut Belanja
            </a>
        </div>
    </div>
</body>

</html>