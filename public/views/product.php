<?php
include("../../config/config.php");
// include("../../components/Navbar.php");

// Cek jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

include('../../components/Aos_animation.php');

// Ambil data produk dari database
$sql = "SELECT id_product, nama_product, deskripsi, gambar, harga, rasa, stok FROM products";
$result = $conn->query($sql);
$products = [];
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
    <title>Produk</title>
    <!-- Gunakan CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @media (max-width: 640px) {
        .grid-cols-1 {
            justify-items: center;
        }
    }
    </style>
</head>

<body class="bg-gray-100" id="product">
    <div class="container mx-auto py-4">
        <h1 class="text-3xl font-bold text-center mb-8 text-indigo-600">Produk Kami</h1>
        <div class="container mx-auto max-w-4xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($products as $product): ?>
                <div class="max-w-xs w-full">
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden hover:shadow-3xl">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-400 to-indigo-600 opacity-75">
                            </div>
                            <img src="../../upload/<?php echo htmlspecialchars($product['gambar']); ?>"
                                alt="<?php echo htmlspecialchars($product['nama_product']); ?>"
                                class="w-full h-48 object-cover object-center relative z-10">
                            <div
                                class="absolute top-4 right-4 bg-gray-100 text-xs font-bold px-3 py-2 rounded-full z-20 transform rotate-12">
                                Best Seller</div>
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">
                                <?php echo htmlspecialchars($product['nama_product']); ?></h2>
                            <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($product['deskripsi']); ?>
                            </p>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-lg font-bold text-indigo-600">Rp
                                    <?php echo number_format($product['harga'], 0, ',', '.'); ?></span>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-sm text-gray-600">4.9 (120 reviews)</span>
                                </div>
                            </div>
                            <button data-id="<?php echo $product['id_product']; ?>"
                                data-nama="<?php echo htmlspecialchars($product['nama_product']); ?>"
                                data-harga="<?php echo $product['harga']; ?>"
                                data-stok="<?php echo $product['stok']; ?>"
                                class="beliBtn w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                                Beli
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal Pembelian -->
        <div id="beliModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-20">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-800" id="modalNama"></h2>
                <p class="mb-2 text-gray-600">Harga: <span id="modalHarga" class="text-indigo-600"></span></p>
                <p class="mb-4 text-gray-600">Stok Tersedia: <span id="modalStok"></span></p>
                <form action="checkout.php" method="POST" id="beliForm">
                    <input type="hidden" name="id_product" id="modalId">
                    <div class="mb-4">
                        <label for="jumlah" class="block text-gray-700">Jumlah yang dibeli:</label>
                        <input type="number" id="jumlah" name="jumlah" min="1" class="w-full border rounded px-3 py-2"
                            required>
                    </div>
                    <!-- Form checkout dapat dilanjutkan di halaman checkout -->
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        Lanjut ke Checkout
                    </button>
                </form>
                <button id="closeModal"
                    class="mt-4 w-full bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                    Batal
                </button>
            </div>
        </div>

        <script>
        // Buka modal ketika tombol Beli diklik
        const modal = document.getElementById("beliModal");
        const modalNama = document.getElementById("modalNama");
        const modalHarga = document.getElementById("modalHarga");
        const modalStok = document.getElementById("modalStok");
        const modalId = document.getElementById("modalId");
        const beliBtns = document.querySelectorAll(".beliBtn");
        const closeModal = document.getElementById("closeModal");

        beliBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const harga = this.dataset.harga;
                const stok = this.dataset.stok;

                modalId.value = id;
                modalNama.textContent = nama;
                modalHarga.textContent = "Rp " + Number(harga).toLocaleString("id-ID");
                modalStok.textContent = stok;

                // set max attribute untuk input jumlah berdasarkan stok
                document.getElementById("jumlah").setAttribute("max", stok);

                modal.classList.remove("hidden");
            });
        });

        closeModal.addEventListener("click", function() {
            modal.classList.add("hidden");
        });
        </script>
</body>

</html>