<?php
session_start();
include("../../config/config.php");

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

// Ambil ongkir dari database
$ongkir = 0;
$daerah = "";
$ongkir_query = "SELECT * FROM ongkir LIMIT 1";
$ongkir_result = $conn->query($ongkir_query);
if ($ongkir_result && $ongkir_result->num_rows > 0) {
    $ongkir_data = $ongkir_result->fetch_assoc();
    $ongkir = $ongkir_data['tarif'];
    $daerah = $ongkir_data['daerah'];
}

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

$total_bayar = $total_harga + $ongkir;

// Cek apakah bukti pembayaran sudah pernah di-upload
$payment_uploaded = false;
$payment_data = null;
if ($id_order) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE id_order = ?");
    $stmt->bind_param("i", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $payment_uploaded = true;
        $payment_data = $row;
    }
    $stmt->close();
}

// Handle upload
$upload_error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$payment_uploaded) {
    $id_order = $_POST['id_order'];
    $id_bank = $_POST['bank'];
    $nama_bank = $_POST['nama_bank'];

    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($_FILES["bukti_pembayaran"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if ($check === false) {
        $upload_error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $upload_error = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["bukti_pembayaran"]["size"] > 500000) {
        $upload_error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        $upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        header("Location: success.php?error=" . urlencode($upload_error));
        exit;
    } else {
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO payments (id_order, id_bank, nama_bank, bukti_pembayaran, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iiss", $id_order, $id_bank, $nama_bank, $target_file);
            $stmt->execute();
            $stmt->close();
            // Sukses, redirect ke status_pesanan.php dengan pesan sukses
            header("Location: status_pesanan.php?success=1");
            exit;
        } else {
            header("Location: success.php?error=" . urlencode("Sorry, there was an error uploading your file."));
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    .font-global {
        font-family: "Poppins", sans-serif;
    }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-100 via-indigo-200 to-indigo-300 min-h-screen font-global">
    <div class="max-w-2xl mx-auto my-10 p-8 bg-white shadow-xl rounded-3xl border border-indigo-200">
        <div class="flex justify-end">
            <a href="index.php"
                class="inline-block bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition font-semibold shadow">
                Kembali
            </a>
        </div>
        <h1 class="text-3xl font-bold mb-6 text-indigo-700">Terima Kasih Sudah Memesan</h1>
        <p class="mb-4 text-gray-700">Silahkan bayar sesuai dengan yang dipesan tadi dan kirim bukti pembayarannya.</p>
        <div class="mb-4 text-gray-700">
            <p>Ongkos Kirim (<?php echo htmlspecialchars($daerah); ?>):
                <strong>Rp <?php echo number_format($ongkir, 0, ',', '.'); ?></strong>
            </p>
            <p>Total yang harus dibayarkan:
                <strong class="text-indigo-600">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></strong>
            </p>
        </div>
        <p class="mb-4 text-gray-700">Harap Diperhatikan ya bang/kak🙏🙏</p>

        <?php if ($payment_uploaded): ?>
        <div class="mt-8 p-6 bg-green-50 border border-green-200 rounded-xl text-green-700">
            <h2 class="text-xl font-semibold mb-2">Bukti Pembayaran Sudah Diupload</h2>
            <p>Terima kasih, bukti pembayaran kamu sudah kami terima dan sedang diproses.</p>
            <p class="mt-2"><strong>Status:</strong> <?php echo htmlspecialchars($payment_data['status']); ?></p>
            <p class="mt-2"><strong>Bank:</strong> <?php echo htmlspecialchars($payment_data['nama_bank']); ?></p>
            <p class="mt-2"><strong>Tanggal Upload:</strong>
                <?php echo htmlspecialchars($payment_data['created_at'] ?? '-'); ?></p>
            <?php if (!empty($payment_data['bukti_pembayaran']) && file_exists($payment_data['bukti_pembayaran'])): ?>
            <img src="<?php echo htmlspecialchars($payment_data['bukti_pembayaran']); ?>" alt="Bukti Pembayaran"
                class="mt-4 rounded-lg shadow max-h-40">
            <?php endif; ?>
        </div>
        <?php else: ?>
        <h2 class="text-xl font-semibold mt-8 mb-4 text-indigo-700">Pilih Bank untuk Pembayaran</h2>
        <form id="paymentForm" action="" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Pilih Bank</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <?php foreach ($bank_list as $bank): ?>
                    <div class="bank-card border-2 border-indigo-200 rounded-xl p-4 cursor-pointer hover:bg-indigo-100 transition"
                        data-id-bank="<?php echo $bank['id_bank']; ?>"
                        data-nama-bank="<?php echo htmlspecialchars($bank['nama_bank']); ?>"
                        data-nomor-rekening="<?php echo htmlspecialchars($bank['nomor_rekening']); ?>">
                        <p class="font-semibold text-indigo-700"><?php echo htmlspecialchars($bank['nama_bank']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="bank" name="bank" required>
                <input type="hidden" id="nama_bank" name="nama_bank" required>
                <p id="nomor_rekening" class="mt-4 text-indigo-700 font-medium"></p>
            </div>
            <label for="bukti_pembayaran"
                class="flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-indigo-400 rounded-xl cursor-pointer hover:bg-indigo-50 transition">
                <svg class="w-8 h-8 text-indigo-500 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 0L8 8m4-4l4 4"></path>
                </svg>
                <span id="file_name_display" class="text-indigo-500 font-medium text-base">Upload File</span>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" class="hidden" required>
            </label>
            <img id="preview_image" class="hidden mt-4 rounded-lg shadow max-h-40" alt="Preview">
            <input class="mt-4" type="hidden" name="id_order" value="<?php echo $id_order; ?>">
            <button type="submit"
                class="mt-6 w-full flex items-center justify-center bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition font-semibold shadow">
                Kirim
            </button>
        </form>
        <?php endif; ?>
    </div>
    <script>
    // Bank card selection
    document.querySelectorAll('.bank-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.bank-card').forEach(c => c.classList.remove('border-indigo-500',
                'ring-2', 'ring-indigo-400'));
            this.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-400');
            var idBank = this.getAttribute('data-id-bank');
            var namaBank = this.getAttribute('data-nama-bank');
            var nomorRekening = this.getAttribute('data-nomor-rekening');
            document.getElementById('bank').value = idBank;
            document.getElementById('nama_bank').value = namaBank;
            document.getElementById('nomor_rekening').textContent = 'Nomor Rekening: ' + nomorRekening;
        });
    });

    // File input preview & validation
    var fileInput = document.getElementById('bukti_pembayaran');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = fileInput.files[0];
            document.getElementById('file_name_display').textContent = file ? file.name : 'Upload File';
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview_image');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // SweetAlert error from PHP
    <?php if (isset($_GET['error'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?php echo htmlspecialchars($_GET['error']); ?>',
        confirmButtonColor: '#a78bfa'
    });
    <?php endif; ?>

    // Form submit with SweetAlert
    var paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(event) {
            event.preventDefault();
            // Validasi file size & type di client-side juga
            const fileInput = document.getElementById('bukti_pembayaran');
            const file = fileInput.files[0];
            if (!file) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silakan upload bukti pembayaran!',
                    confirmButtonColor: '#a78bfa'
                });
                return;
            }
            if (file.size > 500000) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 500KB.',
                    confirmButtonColor: '#a78bfa'
                });
                return;
            }
            // Submit form jika lolos validasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Pembayaranmu sedang diproses. ',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6C3FF4FF'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }
    </script>
</body>

</html>