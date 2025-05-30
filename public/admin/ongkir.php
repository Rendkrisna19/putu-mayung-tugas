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
    <?php
    include("../../components/Slidebar.php")

    ?>
    <div class="flex-grow p-6 transition-all duration-300">
        <h1 class="text-2xl font-bold mb-4 text-indigo-600">Dashboard Ongkir</h1>

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

        <!-- Form Tambah Ongkir -->
        <form action="" method="POST" class="mb-8">
            <input type="hidden" name="add_ongkir" value="1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="daerah" class="block text-gray-700 font-medium">Daerah</label>
                    <input type="text" id="daerah" name="daerah" required class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div>
                    <label for="tarif" class="block text-gray-700 font-medium">Tarif (Rp)</label>
                    <input type="number" id="tarif" name="tarif" required class="w-full border rounded px-3 py-2 mt-1">
                </div>
            </div>
            <div class="mt-4">
                <label for="keterangan" class="block text-gray-700 font-medium">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2"
                    class="w-full border rounded px-3 py-2 mt-1"></textarea>
            </div>
            <button type="submit"
                class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Tambah Ongkir
            </button>
        </form>

        <!-- Daftar Ongkir -->
        <h2 class="text-xl font-semibold mb-3">Daftar Ongkir</h2>
        <?php if (count($ongkir_list) > 0): ?>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2 text-left">#</th>
                    <th class="border px-4 py-2 text-left">Daerah</th>
                    <th class="border px-4 py-2 text-left">Tarif (Rp)</th>
                    <th class="border px-4 py-2 text-left">Keterangan</th>
                    <th class="border px-4 py-2 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ongkir_list as $index => $ongkir): ?>
                <tr class="<?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?>">
                    <td class="border px-4 py-2"><?php echo $index + 1; ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($ongkir['daerah']); ?></td>
                    <td class="border px-4 py-2"><?php echo number_format($ongkir['tarif'], 0, ',', '.'); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($ongkir['keterangan']); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($ongkir['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-gray-700">Belum ada data ongkir.</p>
        <?php endif; ?>
    </div>
</body>

</html>