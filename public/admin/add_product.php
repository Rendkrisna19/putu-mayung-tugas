<?php
session_start();
include("../../config/config.php");



// Proses submit form tambah produk
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan sanitasi input
    $nama_product = $conn->real_escape_string($_POST['nama_product']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $harga = (int) $_POST['harga'];
    $rasa = $conn->real_escape_string($_POST['rasa']);
    $stok = (int) $_POST['stok'];

    // Proses upload gambar
    $gambar = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Tentukan direktori upload
        $uploadDir = "../../upload/";
        // Buat nama file unik
        $fileName = time() . "_" . basename($_FILES['gambar']['name']);
        $targetFile = $uploadDir . $fileName;
        // Cek tipe file (misalnya hanya izinkan jpg, jpeg, png, gif)
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowedTypes)) {
            // Pindahkan file ke folder upload
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
                $gambar = $conn->real_escape_string($fileName);
            } else {
                $error = "Gagal mengunggah file gambar.";
            }
        } else {
            $error = "Hanya file gambar (JPG, JPEG, PNG, GIF) yang diperbolehkan.";
        }
    } else {
        // Jika tidak ada file yang diupload, bisa diatur error atau biarkan kosong
        $gambar = "";
    }

    // Jika tidak ada error upload, simpan data ke database
    if (!isset($error)) {
        // Query INSERT dengan kolom gambar
        $sql = "INSERT INTO products (nama_product, deskripsi, gambar, harga, rasa, stok)
VALUES ('$nama_product', '$deskripsi', '$gambar', $harga, '$rasa', $stok)";

        if ($conn->query($sql) === TRUE) {
            $success = "Produk berhasil ditambahkan.";
        } else {
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="./css/style.css">
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


.font-global {
    font-family: "Poppins", sans-serif;
    font-weight: 400;
    font-style: normal;
}
</style>

<body class="bg-white flex min-h-screen font-global">
    <div class=" min-h-screen"></div>
    <!-- Sidebar -->
    <?php include("../../components/Slidebar.php")
    ?>
    <!-- Konten Utama -->
    <div class="min-h-screen">
        <div class="p-6 flex-1 overflow-y-auto">
            <div class="flex justify-between items-center mb-2">
                <h1 class="text-3xl font-extrabold text-indigo-700 tracking-tight">Tambah Produk Baru</h1>
            </div>

            <!-- Pesan sukses/error -->
            <?php if (isset($success)): ?>
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                <?php echo $success; ?>
            </div>
            <?php elseif (isset($error)): ?>
            <div class="mb-4 p-3 bg-red-200 text-red-800 rounded">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="mb-6">
                            <label for="nama_product" class="block text-gray-700 font-semibold mb-2">Nama Produk</label>
                            <input type="text" id="nama_product" name="nama_product" required
                                class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div class="mb-6">
                            <label for="harga" class="block text-gray-700 font-semibold mb-2">Harga (Rp)</label>
                            <input type="number" id="harga" name="harga" required
                                class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div class="mb-6">
                            <label for="stok" class="block text-gray-700 font-semibold mb-2">Stok</label>
                            <input type="number" id="stok" name="stok" required
                                class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div class="mb-6">
                            <label for="rasa" class="block text-gray-700 font-semibold mb-2">Rasa</label>
                            <input type="text" id="rasa" name="rasa" required
                                class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                    </div>
                    <div>
                        <div class="mb-6">
                            <label for="deskripsi" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="7" required
                                class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 transition"></textarea>
                        </div>
                        <div class="mb-6">
                            <label for="gambar" class="block text-gray-700 font-semibold mb-2">Gambar Produk</label>
                            <input type="file" id="gambar" name="gambar" accept="image/*"
                                class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 transition bg-gray-50">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:scale-105 hover:from-indigo-600 hover:to-blue-600 transition-all duration-200">
                        <span class="inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Produk
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <script>

        </script>
</body>

</html>