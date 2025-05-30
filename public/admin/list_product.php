<?php
session_start();
include("../../config/config.php");

// Ambil data produk dari database
$products = [];
$sql = "SELECT * FROM products ORDER BY id_product DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>List Produk</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-somehashhere" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


    .font-global {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: normal;
    }
    </style>
</head>

<body class="bg-white font-global">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <?php include('../../components/Slidebar.php'); ?>

        <!-- Konten Utama -->
        <div class="flex-grow p-6 transition-all duration-300">
            <h1 class="text-3xl font-bold mb-6 text-indigo-600">Daftar Produk</h1>

            <!-- Pesan Sukses atau Error -->
            <?php if(isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>

            <div class="bg-white p-4 rounded shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(!empty($products)): ?>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td class="px-4 py-2">
                                <?php if(!empty($product['gambar'])): ?>
                                <img src="../../upload/<?php echo htmlspecialchars($product['gambar']); ?>"
                                    alt="<?php echo htmlspecialchars($product['nama_product']); ?>"
                                    class="w-16 h-16 object-cover rounded">
                                <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded">
                                    <span class="text-gray-500 text-xs">No Img</span>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($product['nama_product']); ?></td>
                            <td class="px-4 py-2">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($product['stok']); ?></td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <!-- Tombol Edit (memanggil modal edit) -->
                                <button
                                    class="editBtn inline-block bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition"
                                    data-id="<?php echo $product['id_product']; ?>"
                                    data-nama="<?php echo htmlspecialchars($product['nama_product']); ?>"
                                    data-deskripsi="<?php echo htmlspecialchars($product['deskripsi']); ?>"
                                    data-harga="<?php echo $product['harga']; ?>"
                                    data-stok="<?php echo $product['stok']; ?>"
                                    data-rasa="<?php echo htmlspecialchars($product['rasa']); ?>"
                                    data-gambar="<?php echo htmlspecialchars($product['gambar']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <form action="hapus_product.php" method="POST" class="inline-block"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    <input type="hidden" name="id_product"
                                        value="<?php echo $product['id_product']; ?>">
                                    <button type="submit"
                                        class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">Belum ada produk.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Produk -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-3">
            <h2 class="text-lg font-bold mb-2">Edit Produk</h2>
            <form id="editForm" action="edit_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_product" id="edit_id">
                <div class="mb-2">
                    <label for="edit_nama_product" class="block text-gray-700 text-sm font-medium">Nama Produk</label>
                    <input type="text" id="edit_nama_product" name="nama_product" required
                        class="w-full border rounded px-2 py-1 mt-1 text-sm">
                </div>
                <div class="mb-2">
                    <label for="edit_deskripsi" class="block text-gray-700 text-sm font-medium">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="2" required
                        class="w-full border rounded px-2 py-1 mt-1 text-sm"></textarea>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label for="edit_harga" class="block text-gray-700 text-sm font-medium">Harga (Rp)</label>
                        <input type="number" id="edit_harga" name="harga" required
                            class="w-full border rounded px-2 py-1 mt-1 text-sm">
                    </div>
                    <div>
                        <label for="edit_stok" class="block text-gray-700 text-sm font-medium">Stok</label>
                        <input type="number" id="edit_stok" name="stok" required
                            class="w-full border rounded px-2 py-1 mt-1 text-sm">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="edit_rasa" class="block text-gray-700 text-sm font-medium">Rasa</label>
                    <input type="text" id="edit_rasa" name="rasa" required
                        class="w-full border rounded px-2 py-1 mt-1 text-sm">
                </div>
                <div class="mb-2">
                    <label for="edit_gambar" class="block text-gray-700 text-sm font-medium">Gambar Produk
                        (Opsional)</label>
                    <input type="file" id="edit_gambar" name="gambar" accept="image/*"
                        class="w-full border rounded px-2 py-1 mt-1 text-sm">
                    <div id="currentImage" class="mt-1"></div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeEditModal"
                        class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script untuk Modal Edit -->
    <script>
    const editModal = document.getElementById('editModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editButtons = document.querySelectorAll('.editBtn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Ambil data produk dari data attribute
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const deskripsi = this.dataset.deskripsi;
            const harga = this.dataset.harga;
            const stok = this.dataset.stok;
            const rasa = this.dataset.rasa;
            const gambar = this.dataset.gambar;

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama_product').value = nama;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_stok').value = stok;
            document.getElementById('edit_rasa').value = rasa;

            const currentImageDiv = document.getElementById('currentImage');
            if (gambar) {
                currentImageDiv.innerHTML =
                    `<img src="../../upload/${gambar}" alt="${nama}" class="w-24 h-24 object-cover rounded">`;
            } else {
                currentImageDiv.innerHTML = `<span class="text-gray-500">Tidak ada gambar</span>`;
            }

            editModal.classList.remove('hidden');
        });
    });

    closeEditModal.addEventListener('click', function() {
        editModal.classList.add('hidden');
    });
    </script>
</body>

</html>