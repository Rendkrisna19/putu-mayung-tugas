<?php
session_start();
include ("../../components/Navbar.php");

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
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


.font-global {
    font-family: "Poppins", sans-serif;
    font-weight: 400;
    font-style: normal;
}
</style>


<body class="bg-gray-100  font-global ">

    <section class="py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <!-- Section Header -->
            <h1 class="text-3xl font-bold text-indigo-600 mb-6">📦 Status Pesanan Saya</h1>

            <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-gray-700">
                    <thead class="bg-indigo-100 text-indigo-700 text-xs uppercase font-semibold">
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
            <!-- Tambahkan ini tepat setelah </table> tapi masih di dalam <div class="bg-white ..."> -->

            <div class="mt-8 p-6 bg-indigo-50 rounded-lg shadow-inner text-indigo-700">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                    <!-- Truck icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m16 0v-6a2 2 0 00-2-2h-2m-4 0h4m4 6h-4m-4 0v4m0-4h4" />
                    </svg>
                    Estimasi Status Perjalanan Kurir
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">

                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10h1l1 2 1-2h1m10 0h1l1 2 1-2h1M5 15h14a2 2 0 010 4H5a2 2 0 010-4z" />
                        </svg>
                        <h3 class="font-semibold">Pesanan Diterima</h3>
                        <p class="text-sm text-gray-600">Pesanan kamu sudah kami terima dan sedang diproses.</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5-6l3 3-3 3" />
                        </svg>
                        <h3 class="font-semibold">Sedang Dikemas</h3>
                        <p class="text-sm text-gray-600">Pesanan sedang dikemas untuk pengiriman.</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m16 0v-6a2 2 0 00-2-2h-2m-4 0h4m4 6h-4m-4 0v4m0-4h4" />
                        </svg>
                        <h3 class="font-semibold">Dikirim</h3>
                        <p class="text-sm text-gray-600">Kurir sudah membawa paket ke alamat kamu.</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <h3 class="font-semibold">Pesanan Diterima</h3>
                        <p class="text-sm text-gray-600">Pesanan sudah sampai dan diterima oleh kamu.</p>
                    </div>

                </div>
            </div>
            <!-- Bagian animasi gambar kurir goyang-goyang -->
            <div class="mt-6 flex justify-center">
                <img src="https://png.pngtree.com/png-clipart/20240115/original/pngtree-takeaway-electric-vehicle-ai-delivery-elements-three-dimensional-buckle-free-pattern-png-image_14121739.png"
                    alt="Kurir Delivery" class="w-80 h-80 animate-wiggle" style="max-width: 360px;" />
            </div>

            <style>
            @keyframes wiggle {

                0%,
                100% {
                    transform: rotate(0deg);
                }

                25% {
                    transform: rotate(5deg);
                }

                50% {
                    transform: rotate(0deg);
                }

                75% {
                    transform: rotate(-5deg);
                }
            }

            .animate-wiggle {
                animation: wiggle 2s ease-in-out infinite;
            }
            </style>


        </div>

</body>

</html>