<?php
session_start();
include("../../config/config.php");

// Proses update status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['status']);

    $valid_status = ["pending", "diterima", "ditolak", "sedang dikemas", "dikirim"];
    if (!in_array($status, $valid_status)) {
        $_SESSION['error'] = "Status tidak valid.";
    } else {
        $stmt = $conn->prepare("UPDATE order_items SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Status pesanan berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Gagal memperbarui status.";
        }
        $stmt->close();
    }
    header("Location: daftar_pesanan.php");
    exit;
}

// Ambil data pesanan
$sql = "SELECT 
    order_items.id AS order_id,
    products.nama_product AS product_name,
    order_items.jumlah,
    order_items.status,
    payments.bukti_pembayaran,
    orders.alamat,
    orders.ongkir,
    orders.total_harga,
    orders.created_at
FROM order_items 
JOIN products ON order_items.id_product = products.id_product
LEFT JOIN payments ON order_items.id_order = payments.id_order
LEFT JOIN orders ON order_items.id_order = orders.id
ORDER BY order_items.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Daftar Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    .font-global {
        font-family: "Poppins", sans-serif;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 24px;
        border-radius: 10px;
        max-width: 480px;
        width: 90%;
    }
    </style>
</head>

<body class="bg-gray-50 font-global">
    <div class="min-h-screen flex">
        <?php include('../../components/Slidebar.php'); ?>
        <div class="flex-1 p-6">
            <h1 class="text-3xl font-bold mb-6 text-indigo-600">📦 Daftar Pesanan</h1>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"><?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-3 bg-red-200 text-red-800 rounded"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); endif; ?>

            <div class="mb-4">
                <input type="text" id="searchInput" placeholder="Cari berdasarkan status atau produk..."
                    class="border border-gray-300 rounded px-4 py-2 w-full max-w-md focus:ring focus:outline-none" />
            </div>

            <div class="overflow-x-auto bg-white p-4 rounded shadow-md">
                <table class="min-w-full divide-y divide-gray-300 text-sm text-gray-700" id="orderTable">
                    <thead class="bg-gray-100 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Produk</th>
                            <th class="px-4 py-2 text-left">Jumlah</th>
                            <th class="px-4 py-2 text-left">Bukti</th>
                            <th class="px-4 py-2 text-left">Alamat</th>
                            <th class="px-4 py-2 text-left">Ongkir</th>
                            <th class="px-4 py-2 text-left">Total</th>
                            <th class="px-4 py-2 text-left">Waktu</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $color = match ($row["status"]) {
                                "pending" => "bg-gray-400",
                                "diterima" => "bg-green-500",
                                "ditolak" => "bg-red-500",
                                "sedang dikemas" => "bg-yellow-500",
                                "dikirim" => "bg-blue-500",
                                default => "bg-gray-300"
                            };
                        ?>
                        <tr>
                            <td class="px-4 py-2"><?= $row["order_id"] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row["product_name"]) ?></td>
                            <td class="px-4 py-2"><?= $row["jumlah"] ?></td>
                            <td class="px-4 py-2">
                                <?php if (!empty($row["bukti_pembayaran"])): ?>
                                <img src="../uploads/<?= htmlspecialchars($row["bukti_pembayaran"]) ?>" alt="Bukti"
                                    class="w-16 h-16 object-cover rounded cursor-pointer"
                                    onclick="openImageModal(this.src)" />
                                <?php else: ?>
                                <span class="text-gray-500">Tidak Ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row["alamat"]) ?></td>
                            <td class="px-4 py-2">Rp<?= number_format($row["ongkir"], 0, ',', '.') ?></td>
                            <td class="px-4 py-2">Rp<?= number_format($row["total_harga"], 0, ',', '.') ?></td>
                            <td class="px-4 py-2"><?= date("d M Y H:i", strtotime($row["created_at"])) ?></td>
                            <td class="px-4 py-2">
                                <span class="text-white px-2 py-1 rounded <?= $color ?>">
                                    <?= ucfirst($row["status"]) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <button
                                    onclick="openEditModal(<?= $row["order_id"] ?>, '<?= htmlspecialchars($row["status"]) ?>')"
                                    class="text-indigo-600 hover:text-indigo-800" title="Edit Status"><i
                                        class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-gray-500">Tidak ada pesanan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div id="editStatusModal" class="modal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Edit Status Pesanan</h2>
            <form method="POST" action="">
                <input type="hidden" name="order_id" id="modalOrderId" required />
                <label for="modalStatus" class="block mb-2 text-sm font-medium">Pilih Status:</label>
                <select name="status" id="modalStatus" class="w-full border rounded p-2 mb-4" required>
                    <option value="pending">Pending</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="sedang dikemas">Sedang Dikemas</option>
                    <option value="dikirim">Dikirim</option>
                </select>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Image -->
    <div id="imageModal" class="modal" onclick="closeImageModal()">
        <img id="modalImage" src="" alt="Bukti Pembayaran" class="max-w-full max-h-full rounded shadow-lg" />
    </div>

    <script>
    function openEditModal(orderId, status) {
        document.getElementById('modalOrderId').value = orderId;
        document.getElementById('modalStatus').value = status;
        document.getElementById('editStatusModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editStatusModal').style.display = 'none';
    }

    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    $('#searchInput').on('input', function() {
        const val = $(this).val().toLowerCase();
        $('#orderTable tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(val));
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            closeEditModal();
            closeImageModal();
        }
    });
    </script>
</body>

</html>