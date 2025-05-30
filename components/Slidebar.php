<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    /* Custom styling untuk transisi sidebar */
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
                <span id="sidebar-title" class="text-lg font-bold hidden select-none">Admin Panel</span>
                <button id="toggleSidebar" class="focus:outline-none" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <!-- Menu Sidebar -->
            <nav class="flex-1 py-4 overflow-auto">
                <ul class="space-y-2">
                    <li>
                        <a href="../admin/index.php" class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-tachometer-alt text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/add_product.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-box-open text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Tambah Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/list_product.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-list text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Daftar Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/ongkir.php" class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-shipping-fast text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Ongkos Kirim</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/daftar_pesanan.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-receipt text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Daftar Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/antrian_pesanan.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-tasks text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Antrian Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/list_penjualan.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-chart-line text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Laporan Penjualan</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/list_user.php"
                            class="flex items-center px-4 py-2 hover:bg-blue-700 transition">
                            <i class="fas fa-users text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Manajemen User</span>
                        </a>
                    </li>
                    <div class="px-4 py-4 border-t border-blue-700">
                        <a href="../../auth/admin/auth.php" class="flex items-center hover:text-red-400 transition">
                            <i class="fas fa-sign-out-alt text-lg w-6 text-center"></i>
                            <span class="ml-4 hidden menu-text">Logout</span>
                        </a>
                    </div>
                </ul>
            </nav>
        </div>


        <!-- Script untuk toggle sidebar dengan simpan state di localStorage -->
        <script>
        const sidebar = document.getElementById('sidebar');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebarTitle = document.getElementById('sidebar-title');
        const menuTexts = document.querySelectorAll('.menu-text');

        // Ambil state sidebar dari localStorage
        let isExpanded = localStorage.getItem('sidebarExpanded') === 'true';

        function setSidebarState(expanded) {
            if (expanded) {
                sidebar.style.width = '200px';
                sidebarTitle.classList.remove('hidden');
                menuTexts.forEach(text => text.classList.remove('hidden'));
            } else {
                sidebar.style.width = '60px';
                sidebarTitle.classList.add('hidden');
                menuTexts.forEach(text => text.classList.add('hidden'));
            }
            localStorage.setItem('sidebarExpanded', expanded);
            isExpanded = expanded;
        }

        // Atur state saat halaman load
        setSidebarState(isExpanded);

        toggleSidebar.addEventListener('click', () => {
            setSidebarState(!isExpanded);
        });
        </script>
</body>

</html>