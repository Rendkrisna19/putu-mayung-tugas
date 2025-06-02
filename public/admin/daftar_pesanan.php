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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    body,
    .font-global {
        font-family: 'Inter', sans-serif;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(30, 41, 59, 0.7);
        justify-content: center;
        align-items: center;
        transition: background 0.3s;
    }

    .modal-content {
        background: #fff;
        padding: 2rem 2.5rem;
        border-radius: 1.25rem;
        max-width: 480px;
        width: 95%;
        box-shadow: 0 8px 32px rgba(30, 41, 59, 0.15);
        animation: popIn 0.2s;
    }

    @keyframes popIn {
        from {
            transform: scale(0.95);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .swal2-popup {
        font-family: 'Inter', sans-serif !important;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-100 to-indigo-50 font-global min-h-screen">
    <div class="min-h-screen flex">
        <?php include('../../components/Slidebar.php'); ?>
        <div class="flex-1 p-8">
            <h1 class="text-4xl font-bold mb-8 text-indigo-700 tracking-tight drop-shadow">📦 Daftar Pesanan</h1>

            <div class="mb-6">
                <input type="text" id="searchInput" placeholder="Cari status atau produk..."
                    class="border border-indigo-200 rounded-lg px-5 py-3 w-full max-w-md focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow transition" />
            </div>

            <div class="overflow-x-auto bg-white/80 p-6 rounded-2xl shadow-xl">
                <table class="min-w-full divide-y divide-indigo-100 text-sm text-slate-700" id="orderTable">
                    <thead class="bg-indigo-50 text-xs uppercase font-semibold text-indigo-700">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-left">Jumlah</th>
                            <th class="px-4 py-3 text-left">Bukti</th>
                            <th class="px-4 py-3 text-left">Alamat</th>
                            <th class="px-4 py-3 text-left">Ongkir</th>
                            <th class="px-4 py-3 text-left">Total</th>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-50 bg-white">
                        <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $color = match ($row["status"]) {
                                "pending" => "bg-slate-400",
                                "diterima" => "bg-emerald-500",
                                "ditolak" => "bg-rose-500",
                                "sedang dikemas" => "bg-amber-400",
                                "dikirim" => "bg-sky-500",
                                default => "bg-slate-300"
                            };
                        ?>
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 font-semibold"><?= $row["order_id"] ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row["product_name"]) ?></td>
                            <td class="px-4 py-3"><?= $row["jumlah"] ?></td>
                            <td class="px-4 py-3">
                                <?php if (!empty($row["bukti_pembayaran"])): ?>
                                <img src="../uploads/<?= htmlspecialchars($row["bukti_pembayaran"]) ?>" alt="Bukti"
                                    class="w-16 h-16 object-cover rounded-lg shadow cursor-pointer border border-indigo-100 hover:scale-105 transition"
                                    onclick="openImageModal(this.src)" />
                                <?php else: ?>
                                <span class="text-slate-400 italic">Tidak Ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row["alamat"]) ?></td>
                            <td class="px-4 py-3">Rp<?= number_format($row["ongkir"], 0, ',', '.') ?></td>
                            <td class="px-4 py-3">Rp<?= number_format($row["total_harga"], 0, ',', '.') ?></td>
                            <td class="px-4 py-3"><?= date("d M Y H:i", strtotime($row["created_at"])) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-white px-3 py-1 rounded-full font-semibold <?= $color ?> shadow">
                                    <?= ucfirst($row["status"]) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    onclick="openEditModal(<?= $row["order_id"] ?>, '<?= htmlspecialchars($row["status"]) ?>')"
                                    class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-full p-2 shadow transition"
                                    title="Edit Status"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-6 text-slate-400">Tidak ada pesanan.</td>
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
            <h2 class="text-2xl font-bold mb-6 text-indigo-700">Edit Status Pesanan</h2>
            <form id="editStatusForm" method="POST" action="">
                <input type="hidden" name="order_id" id="modalOrderId" required />
                <label for="modalStatus" class="block mb-2 text-sm font-medium text-slate-700">Pilih Status:</label>
                <select name="status" id="modalStatus"
                    class="w-full border border-indigo-200 rounded-lg p-3 mb-6 focus:ring-2 focus:ring-indigo-400"
                    required>
                    <option value="pending">Pending</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="sedang dikemas">Sedang Dikemas</option>
                    <option value="dikirim">Dikirim</option>
                </select>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold transition">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 shadow transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Image -->
    <div id="imageModal" class="modal" onclick="closeImageModal()">
        <img id="modalImage" src="" alt="Bukti Pembayaran"
            class="max-w-full max-h-full rounded-xl shadow-2xl border-4 border-white" />
    </div>

    <script>
    // SweetAlert2 for PHP session messages
    <?php if (isset($_SESSION['success'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= addslashes($_SESSION['success']) ?>',
        confirmButtonColor: '#6366f1'
    });
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= addslashes($_SESSION['error']) ?>',
        confirmButtonColor: '#6366f1'
    });
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

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

    // SweetAlert2 confirm before submit
    $('#editStatusForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin update status?',
            text: "Status pesanan akan diubah.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
    </script>
</body>

</html>