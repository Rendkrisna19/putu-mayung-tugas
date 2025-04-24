<?php
session_start();
include "../../config/config.php";

// Ambil daftar bank dari database
$bank_list = [];
$sql = "SELECT * FROM banks ORDER BY nama_bank ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bank_list[] = $row;
    }
}

// Ambil ongkir dari database (misalnya hanya ambil 1 ongkir yang berlaku)
$ongkir = 0;
$daerah = "";
$ongkir_query = "SELECT * FROM ongkir LIMIT 1"; // Ganti sesuai nama tabel ongkir kamu
$ongkir_result = $conn->query($ongkir_query);
if ($ongkir_result && $ongkir_result->num_rows > 0) {
    $ongkir_data = $ongkir_result->fetch_assoc();
    $ongkir = $ongkir_data['tarif'];
    $daerah = $ongkir_data['daerah'];
}

// Ambil id_order dari session
$id_order = $_SESSION['id_order'] ?? 0;

// Ambil total harga dari order_items berdasarkan id_order
$total_harga = 0;
if ($id_order) {
    $stmt = $conn->prepare("SELECT SUM(total) AS total_harga FROM order_items WHERE id_order = ?");
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $total_harga = $row['total_harga'];
    }
    $stmt->close();
}

// Hitung total akhir
$total_bayar = $total_harga + $ongkir;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto my-10 p-6 bg-white shadow rounded">
        <h1 class="text-2xl font-bold mb-6">Terima Kasih Sudah Memesan</h1>
        <p class="mb-4 text-gray-700">Silahkan bayar sesuai dengan yang dipesan tadi yah dan kirim bukti pembayarannya
        </p>
        <div class="mb-4 text-gray-700">
            <p>Ongkos Kirim (<?php echo htmlspecialchars($daerah); ?>):
                <strong>Rp <?php echo number_format($ongkir, 0, ',', '.'); ?></strong>
            </p>
            <p>Total yang harus dibayarkan:
                <strong class="text-blue-600">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></strong>
            </p>
        </div>



        <p class="mb-4 text-gray-700">Harap Diperhatikan ya bang/kak🙏🙏
        </p>

        <!-- Pilihan Bank -->
        <h2 class="text-xl font-semibold mt-8 mb-4">Pilih Bank untuk Pembayaran</h2>
        <form id="paymentForm" action="upload_bukti.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="bank" class="block text-gray-700 font-medium">Pilih Bank</label>
                <select id="bank" name="bank" class="w-full border rounded px-3 py-2" required>
                    <?php if(!empty($bank_list)): ?>
                    <?php foreach($bank_list as $bank): ?>
                    <option value="<?php echo $bank['id_bank']; ?>"
                        data-nama-bank="<?php echo htmlspecialchars($bank['nama_bank']); ?>"
                        data-nomor-rekening="<?php echo htmlspecialchars($bank['nomor_rekening']); ?>">
                        <?php echo htmlspecialchars($bank['nama_bank']); ?>
                    </option>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <option value="">Tidak ada data bank</option>
                    <?php endif; ?>
                </select>
                <input type="hidden" id="nama_bank" name="nama_bank">
                <p id="nomor_rekening" class="mt-2 text-gray-700"></p>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div class="mb-4">
                <label for="bukti_pembayaran" class="block text-gray-700 font-medium">Upload Bukti Pembayaran</label>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" class="w-full border rounded px-3 py-2"
                    required>
            </div>

            <input type="hidden" name="id_order" value="<?php echo $id_order; ?>">

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Upload Bukti Pembayaran
            </button>
        </form>

        <!-- Tombol Kembali ke Beranda -->
        <div class="mt-6">
            <a href="index.php"
                class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
    document.getElementById('bank').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var nomorRekening = selectedOption.getAttribute('data-nomor-rekening');
        document.getElementById('nomor_rekening').textContent = 'Nomor Rekening: ' + nomorRekening;
        document.getElementById('nama_bank').value = selectedOption.getAttribute('data-nama-bank');
    });

    document.getElementById('paymentForm').addEventListener('submit', function(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Terima Kasih!',
            text: 'Pembayaranmu sedang diproses. Silahkan hubungi nomor WA admin berikut ini: 08123456789',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    </script>
</body>

</html>