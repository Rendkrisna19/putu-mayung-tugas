<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-...YOUR_INTEGRITY_HASH_HERE..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    /* Contoh custom styling untuk transisi sidebar */
    .sidebar-transition {
        transition: width 0.3s;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-transition bg-indigo-600 text-white flex flex-col" style="width: 60px;">
            <!-- Header Sidebar -->
            <div class="flex items-center justify-between px-4 py-4 border-b border-blue-700">
                <span id="sidebar-title" class="text-lg font-bold hidden">Admin</span>
                <button id="toggleSidebar" class="focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <!-- Menu Sidebar -->
            <nav class="flex-1 py-4">
                <ul class="space-y-2">
                    <li>
                        <a href="../admin/index.php" class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-tachometer-alt text-lg"></i>
                            <span class="ml-4 hidden menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/add_product.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-box-open text-lg"></i>
                            <span class="ml-4 hidden menu-text">Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/ongkir.php" class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-shipping-fast text-lg"></i>
                            <span class="ml-4 hidden menu-text">Ongkir</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/list_product.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-list text-lg"></i>
                            <span class="ml-4 hidden menu-text">List Product</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/daftar_pesanan.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-receipt text-lg"></i>
                            <span class="ml-4 hidden menu-text">List Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/list_user.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-users text-lg"></i>
                            <span class="ml-4 hidden menu-text">User</span>
                        </a>
                    </li>
                    <!-- Tambahkan menu lainnya -->
                </ul>
            </nav>
            <!-- Footer Sidebar -->
            <!-- <div class="px-4 py-4 border-t border-blue-700">
                <a href="../../public/admin/index.php" class="flex items-center">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                    <span class="ml-4 hidden menu-text">Logout</span>
                </a>
            </div> -->
        </div>
        <!-- Konten Utama -->
        <!-- <div class="flex-1 p-6">
            <h1 class="text-3xl font-bold mb-4">Dashboard Konten</h1>
            <p>Isi konten utama di sini...</p>

            <div class="mt-6 bg-white p-4 rounded shadow">
                <p>Ini adalah konten utama halaman admin. Anda bisa menambahkan grafik, tabel, dan data lainnya di sini.
                </p>
            </div>
        </div> -->
    </div>

    <!-- Script untuk toggle sidebar -->
    <script>
    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebarTitle = document.getElementById('sidebar-title');
    const menuTexts = document.querySelectorAll('.menu-text');

    let isExpanded = false;

    toggleSidebar.addEventListener('click', () => {
        isExpanded = !isExpanded;
        if (isExpanded) {
            sidebar.style.width = '200px';
            sidebarTitle.classList.remove('hidden');
            menuTexts.forEach(text => text.classList.remove('hidden'));
        } else {
            sidebar.style.width = '60px';
            sidebarTitle.classList.add('hidden');
            menuTexts.forEach(text => text.classList.add('hidden'));
        }
    });
    </script>
</body>

</html>