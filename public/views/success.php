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
        <div class="mt-6">
            <a href="index.php"
                class=" position-right inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Kembali
            </a>
        </div>
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

        ```
        <p class="mb-4 text-gray-700">Harap Diperhatikan ya bang/kak🙏🙏
        </p>

        <!-- Pilihan Bank -->
        <!-- Pilihan Bank -->
        <h2 class="text-xl font-semibold mt-8 mb-4">Pilih Bank untuk Pembayaran</h2>
        <form id="paymentForm" action="upload_bukti.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Pilih Bank</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <?php foreach ($bank_list as $bank): ?>
                    <div class="bank-card border rounded-lg p-4 cursor-pointer hover:bg-blue-100 transition"
                        data-id-bank="<?php echo $bank['id_bank']; ?>"
                        data-nama-bank="<?php echo htmlspecialchars($bank['nama_bank']); ?>"
                        data-nomor-rekening="<?php echo htmlspecialchars($bank['nomor_rekening']); ?>">
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($bank['nama_bank']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" id="bank" name="bank">
                <input type="hidden" id="nama_bank" name="nama_bank">

                <!-- Nomor rekening tampil di sini -->
                <p id="nomor_rekening" class="mt-4 text-blue-700 font-medium "></p>
            </div>



            <label for="bukti_pembayaran"
                class="flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-blue-400 rounded-xl cursor-pointer hover:bg-blue-50 transition">
                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 0L8 8m4-4l4 4">
                    </path>
                </svg>
                <span id="file_name_display" class="text-blue-500 font-medium text-base">Upload File</span>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" class="hidden" required>
            </label>



            <input class="mt-4" type="hidden" name="id_order" value="<?php echo $id_order; ?>">

            <button type="submit"
                class=" mt-4 w-400 md:w-600 flex items-center justify-center h-screnn bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Kirim
            </button>
        </form>

        <!-- Tombol Kembali ke Beranda -->

    </div>

    <script>
    document.querySelectorAll('.bank-card').forEach(card => {
        card.addEventListener('click', function() {
            // Hapus border terpilih sebelumnya
            document.querySelectorAll('.bank-card').forEach(c => c.classList.remove('border-blue-500'));
            // Tambahkan border untuk yang terpilih
            this.classList.add('border-blue-500');

            // Ambil data dari atribut
            var idBank = this.getAttribute('data-id-bank');
            var namaBank = this.getAttribute('data-nama-bank');
            var nomorRekening = this.getAttribute('data-nomor-rekening');

            // Set ke hidden input dan tampilkan rekening
            document.getElementById('bank').value = idBank;
            document.getElementById('nama_bank').value = namaBank;
            document.getElementById('nomor_rekening').textContent = 'Nomor Rekening: ' + nomorRekening;
        });
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


    document.getElementById('bukti_pembayaran').addEventListener('change', function() {
        const fileInput = this;
        const fileName = fileInput.files[0] ? fileInput.files[0].name : 'Upload File';
        document.getElementById('file_name_display').textContent = fileName;

        // Tampilkan preview
        if (fileInput.files[0] && fileInput.files[0].type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview_image');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    });
    </script>
    ```

</body>

</html>