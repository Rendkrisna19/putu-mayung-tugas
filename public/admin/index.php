<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: ../../auth/admin/auth.php");
    exit;
}

include("../../config/config.php");

// Total Penjualan
$totalPendapatanQuery = $conn->query("SELECT SUM(total_harga) as total FROM orders");
$totalPendapatan = $totalPendapatanQuery->fetch_assoc()['total'] ?? 0;

// Jumlah Order
$jumlahOrderQuery = $conn->query("SELECT COUNT(*) as jumlah FROM orders");
$jumlahOrder = $jumlahOrderQuery->fetch_assoc()['jumlah'] ?? 0;

// Order Terbaru
$orderTerbaruQuery = $conn->query("SELECT MAX(created_at) as terbaru FROM orders");
$orderTerbaru = $orderTerbaruQuery->fetch_assoc()['terbaru'] ?? '-';

// Data per bulan (untuk chart)
$monthlyQuery = $conn->query("
    SELECT MONTH(created_at) as bulan, SUM(total_harga) as total 
    FROM orders 
    GROUP BY MONTH(created_at)
");
$bulan = [];
$total = [];

while ($row = $monthlyQuery->fetch_assoc()) {
    $bulan[] = DateTime::createFromFormat('!m', $row['bulan'])->format('M');
    $total[] = $row['total'];
}

// Ambil semua data orders untuk tabel
$dataOrders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-p6O7r7R9/1LKZ0jI9bEzGX0EpZ8aNUIq3JevNsqVXWkq/x5Wn2YFEv11hNqw8j5DjyR+6RtH/VgLrfOYBuW2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}
</style>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <div class="flex">
        <?php include("../../components/Slidebar.php"); ?>
        <main class="flex-1 p-6 space-y-6">
            <h1 class="text-3xl font-bold text-indigo-600 flex items-center gap-3">
                <i class="fas fa-chart-line"></i> Dashboard Admin
            </h1>

            <!-- Stat Box -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
                    <div class="text-indigo-600 text-4xl">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Pendapatan</p>
                        <h2 class="text-2xl font-bold text-indigo-600">Rp
                            <?= number_format($totalPendapatan, 0, ',', '.') ?></h2>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
                    <div class="text-blue-600 text-4xl">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Jumlah Order</p>
                        <h2 class="text-2xl font-bold text-blue-600"><?= $jumlahOrder ?></h2>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
                    <div class="text-indigo-600 text-4xl">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Order Terbaru</p>
                        <h2 class="text-xl"><?= date('d M Y - H:i', strtotime($orderTerbaru)) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-indigo-700">
                    <i class="fas fa-chart-area"></i> Grafik Penjualan Bulanan
                </h2>
                <canvas id="salesChart" class="w-full h-64"></canvas>
            </div>

            <!-- Tabel Orders -->
            <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-indigo-700">
                    <i class="fas fa-list"></i> Daftar Order Terbaru
                </h2>
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-indigo-100 text-left text-sm font-semibold text-indigo-700">
                        <tr>
                            <th class="border border-indigo-300 p-2">ID</th>
                            <th class="border border-indigo-300 p-2">Alamat</th>
                            <th class="border border-indigo-300 p-2">Ongkir</th>
                            <th class="border border-indigo-300 p-2">Total</th>
                            <th class="border border-indigo-300 p-2">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $dataOrders->fetch_assoc()): ?>
                        <tr class="hover:bg-indigo-50 text-sm">
                            <td class="border border-indigo-300 p-2"><?= $row['id'] ?></td>
                            <td class="border border-indigo-300 p-2"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td class="border border-indigo-300 p-2">Rp
                                <?= number_format($row['ongkir'], 0, ',', '.') ?></td>
                            <td class="border border-indigo-300 p-2 font-semibold text-indigo-600">Rp
                                <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td class="border border-indigo-300 p-2">
                                <?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($bulan) ?>,
            datasets: [{
                label: 'Total Penjualan',
                data: <?= json_encode($total) ?>,
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.3)', // indigo-500 with opacity
                borderColor: 'rgba(79, 70, 229, 1)', // indigo-600
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                pointRadius: 5,
                pointHoverRadius: 7,
                hoverBorderWidth: 2,
                hoverBackgroundColor: 'rgba(79, 70, 229, 0.5)',
                shadowOffsetX: 0,
                shadowOffsetY: 4,
                shadowBlur: 15,
                shadowColor: 'rgba(79, 70, 229, 0.4)',
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'nearest',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#4f46e5', // Indigo color
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(79, 70, 229, 0.9)',
                    titleFont: {
                        size: 16,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    padding: 10,
                    cornerRadius: 6,
                    displayColors: false,
                    callbacks: {
                        label: context => `Rp ${context.parsed.y.toLocaleString('id-ID')}`
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(99, 102, 241, 0.2)'
                    },
                    ticks: {
                        color: '#4f46e5',
                        font: {
                            weight: '600'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(99, 102, 241, 0.2)'
                    },
                    ticks: {
                        color: '#4f46e5',
                        font: {
                            weight: '600'
                        },
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
    </script>
</body>

</html>