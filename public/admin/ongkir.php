<?php
session_start();
include("../../config/config.php");

// Proses tambah ongkir
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ongkir'])) {
    $daerah = $conn->real_escape_string($_POST['daerah']);
    $tarif  = (int) $_POST['tarif'];
    $keterangan = $conn->real_escape_string($_POST['keterangan']);

    $sql = "INSERT INTO ongkir (daerah, tarif, keterangan) VALUES ('$daerah', $tarif, '$keterangan')";

    if ($conn->query($sql) === TRUE) {
        $success = "Data ongkir berhasil ditambahkan.";
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}

// Ambil data ongkir untuk ditampilkan di dashboard
$sql = "SELECT * FROM ongkir ORDER BY created_at DESC";
$result = $conn->query($sql);
$ongkir_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $ongkir_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Ongkir - Admin</title>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    .font-global {
        font-family: "Poppins", sans-serif;
    }

    .animate-swing {
        animation: swing 2s infinite ease-in-out;
        transform-origin: bottom center;
    }

    @keyframes swing {
        0% {
            transform: rotate(-7deg);
        }

        50% {
            transform: rotate(7deg);
        }

        100% {
            transform: rotate(-7deg);
        }
    }

    .gradient-bg {
        background: linear-gradient(135deg, #7780FFFF 0%, #7B8BDBFF 100%);
    }

    .card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 6px 32px 0 rgba(99, 102, 241, 0.12);
    }

    .table-header {
        background: linear-gradient(90deg, #7B7BEBFF 0%, #7680DDFF 100%);
        color: #fff;
    }

    .table-row:nth-child(even) {
        background: #f3f4f6;
    }

    .table-row:nth-child(odd) {
        background: #fff;
    }
    </style>
</head>

<body class="gradient-bg min-h-screen font-global ">
    <?php include("../../components/Slidebar.php") ?>
    <div class="flex-grow p-6 flex flex-col items-center transition-all duration-300">
        <div class="w-full max-w-4xl">
            <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
                <!-- Animated SVG -->
                <div class="w-40 h-40 flex-shrink-0 flex items-center justify-center">
                    <svg class="animate-swing" width="120" height="120" viewBox="0 0 120 120" fill="none">
                        <ellipse cx="60" cy="110" rx="35" ry="8" fill="#e0e7ff" />
                        <g>
                            <rect x="45" y="60" width="30" height="30" rx="6" fill="#6366f1" />
                            <rect x="50" y="65" width="20" height="20" rx="4" fill="#a5b4fc" />
                            <circle cx="60" cy="55" r="15" fill="#818cf8" />
                            <ellipse cx="60" cy="55" rx="7" ry="10" fill="#fff" opacity="0.7" />
                            <rect x="55" y="80" width="10" height="18" rx="3" fill="#6366f1" />
                            <rect x="45" y="90" width="8" height="18" rx="3" fill="#818cf8" />
                            <rect x="67" y="90" width="8" height="18" rx="3" fill="#818cf8" />
                        </g>
                        <rect x="70" y="70" width="20" height="12" rx="3" fill="#fbbf24" />
                        <rect x="30" y="70" width="20" height="12" rx="3" fill="#fbbf24" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2 text-white drop-shadow">Dashboard Ongkir</h1>
                    <p class="text-lg text-indigo-700">Kelola tarif ongkir dengan mudah dan cepat!</p>
                </div>
            </div>

            <!-- Pesan sukses/error -->
            <?php if (isset($success)): ?>
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded shadow">
                <?php echo $success; ?>
            </div>
            <?php elseif (isset($error)): ?>
            <div class="mb-4 p-3 bg-red-200 text-red-800 rounded shadow">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <!-- Form Tambah Ongkir -->
            <div class="card p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-indigo-700">Tambah Ongkir Baru</h2>
                <form action="" method="POST">
                    <input type="hidden" name="add_ongkir" value="1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="daerah" class="block text-gray-700 font-medium">Daerah</label>
                            <input type="text" id="daerah" name="daerah" required
                                class="w-full border border-indigo-200 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label for="tarif" class="block text-gray-700 font-medium">Tarif (Rp)</label>
                            <input type="number" id="tarif" name="tarif" required
                                class="w-full border border-indigo-200 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="keterangan" class="block text-gray-700 font-medium">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="2"
                            class="w-full border border-indigo-200 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>
                    <button type="submit"
                        class="mt-4 w-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:from-indigo-600 hover:to-blue-600 transition shadow">
                        Tambah Ongkir
                    </button>
                </form>
            </div>

            <!-- Daftar Ongkir -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4 text-indigo-700">Daftar Ongkir</h2>
                <?php if (count($ongkir_list) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse rounded-lg overflow-hidden shadow">
                        <thead>
                            <tr class="table-header">
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Daerah</th>
                                <th class="px-4 py-2 text-left">Tarif (Rp)</th>
                                <th class="px-4 py-2 text-left">Keterangan</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ongkir_list as $index => $ongkir): ?>
                            <tr class="table-row">
                                <td class="px-4 py-2"><?php echo $index + 1; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($ongkir['daerah']); ?></td>
                                <td class="px-4 py-2 text-indigo-700 font-semibold">
                                    <?php echo number_format($ongkir['tarif'], 0, ',', '.'); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($ongkir['keterangan']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($ongkir['created_at']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-gray-700">Belum ada data ongkir.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>