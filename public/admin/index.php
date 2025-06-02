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

// Ambil daftar pengguna
$dataUsers = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 10");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    ::-webkit-scrollbar {
        width: 8px;
        background: #e0e7ff;
    }

    ::-webkit-scrollbar-thumb {
        background: #6366f1;
        border-radius: 8px;
    }

    .card-glass {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        backdrop-filter: blur(4px);
        border-radius: 1rem;
        border: 1px solid rgba(99, 102, 241, 0.08);
    }

    .icon-bg {
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-bg-blue {
        background: linear-gradient(135deg, #38bdf8 0%, #60a5fa 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-bg-green {
        background: linear-gradient(135deg, #22d3ee 0%, #34d399 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table-head {
        background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
        color: #fff;
    }
    </style>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <div class="flex min-h-screen">
        <?php include("../../components/Slidebar.php"); ?>
        <main class="flex-1 p-6 space-y-8">
            <h1 class="text-3xl font-bold text-indigo-700 flex items-center gap-3 mb-2">
                <i class="fa-solid fa-gauge-high"></i> Dashboard Admin
            </h1>

            <!-- Stat Box -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card-glass p-5 flex items-center gap-4">
                    <div class="icon-bg text-3xl shadow-lg">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 font-semibold">Total Pendapatan</p>
                        <h2 class="text-2xl font-bold text-indigo-700">Rp
                            <?= number_format($totalPendapatan, 0, ',', '.') ?></h2>
                    </div>
                </div>
                <div class="card-glass p-5 flex items-center gap-4">
                    <div class="icon-bg-blue text-3xl shadow-lg">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 font-semibold">Jumlah Order</p>
                        <h2 class="text-2xl font-bold text-sky-600"><?= $jumlahOrder ?></h2>
                    </div>
                </div>
                <div class="card-glass p-5 flex items-center gap-4">
                    <div class="icon-bg-green text-3xl shadow-lg">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 font-semibold">Order Terbaru</p>
                        <h2 class="text-xl text-emerald-600">
                            <?= $orderTerbaru && $orderTerbaru != '-' ? date('d M Y - H:i', strtotime($orderTerbaru)) : '-' ?>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="card-glass p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-indigo-700">
                    <i class="fa-solid fa-chart-line"></i> Grafik Penjualan Bulanan
                </h2>
                <canvas id="salesChart" class="w-full h-64"></canvas>
            </div>

            <!-- Tabel Orders -->
            <div class="card-glass p-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-indigo-700">
                    <i class="fa-solid fa-list-check"></i> Daftar Order Terbaru
                </h2>
                <table class="min-w-full border border-indigo-200 rounded-lg overflow-hidden">
                    <thead class="table-head text-left text-sm font-semibold">
                        <tr>
                            <th class="p-2">ID</th>
                            <th class="p-2">Alamat</th>
                            <th class="p-2">Ongkir</th>
                            <th class="p-2">Total</th>
                            <th class="p-2">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $dataOrders->fetch_assoc()): ?>
                        <tr class="hover:bg-indigo-50 text-sm border-b border-indigo-100">
                            <td class="p-2"><?= $row['id'] ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td class="p-2">Rp <?= number_format($row['ongkir'], 0, ',', '.') ?></td>
                            <td class="p-2 font-semibold text-indigo-600">Rp
                                <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td class="p-2"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Users -->

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
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                pointRadius: 6,
                pointHoverRadius: 8,
                hoverBorderWidth: 2,
                hoverBackgroundColor: 'rgba(99, 102, 241, 0.5)',
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
                        color: '#6366f1',
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(99, 102, 241, 0.9)',
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
                        color: 'rgba(99, 102, 241, 0.08)'
                    },
                    ticks: {
                        color: '#6366f1',
                        font: {
                            weight: '600'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(99, 102, 241, 0.08)'
                    },
                    ticks: {
                        color: '#6366f1',
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