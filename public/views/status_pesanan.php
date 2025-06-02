<?php
session_start();
include("../../config/config.php");

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query untuk ambil data pesanan user
// Saya asumsikan status ada di tabel orders, kalau di order_items bisa disesuaikan
$sql = "SELECT 
    o.id AS order_id,
    p.nama_product AS product_name,
    oi.jumlah,
    p.harga,
    (oi.jumlah * p.harga) AS total,
    -- Ambil status dari orders, jika statusnya ada di order_items, ganti oi.status
    oi.status,
    pay.bukti_pembayaran,
    o.created_at
FROM order_items oi
JOIN orders o ON oi.id_order = o.id
JOIN products p ON oi.id_product = p.id_product
LEFT JOIN payments pay ON o.id = pay.id_order
WHERE o.user_id = ?
ORDER BY o.created_at DESC, oi.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Status Pesanan Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans min-h-screen">

    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-purple-600 mb-6">📦 Status Pesanan Saya</h1>

        <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
            <table class="min-w-full table-auto text-sm text-gray-700">
                <thead class="bg-purple-100 text-purple-700 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">ID Pesanan</th>
                        <th class="px-4 py-3 text-left">Produk</th>
                        <th class="px-4 py-3 text-left">Jumlah</th>
                        <th class="px-4 py-3 text-left">Harga Satuan</th>
                        <th class="px-4 py-3 text-left">Total Harga</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Tanggal Pesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                            // Default status jika null atau kosong
                            $status = $row['status'] ?? 'pending';

                            // Warna badge status
                            $badge = match (strtolower($status)) {
                                'pending' => 'bg-gray-400 text-white',
                                'diterima' => 'bg-green-500 text-white',
                                'ditolak' => 'bg-red-500 text-white',
                                'sedang dikemas' => 'bg-yellow-400 text-black',
                                'dikirim' => 'bg-blue-500 text-white',
                                default => 'bg-gray-300 text-black',
                            };
                            ?>
                    <tr>
                        <td class="px-4 py-2"><?= $row['order_id'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['product_name']) ?></td>
                        <td class="px-4 py-2"><?= $row['jumlah'] ?></td>
                        <td class="px-4 py-2">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td class="px-4 py-2">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold <?= $badge ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2"><?= date("d M Y H:i", strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">Belum ada pesanan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>