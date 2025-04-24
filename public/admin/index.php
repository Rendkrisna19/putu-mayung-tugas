<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: ../../auth/admin/auth.php");
    exit;
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex min-h-screen">
    <?php include("../../components/Slidebar.php"); ?>

    <div class="flex-1 p-6">
        <h1 class="text-3xl font-bold mb-4">Dashboard</h1>

        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Waktu</h2>
            <div id="clock" class="mb-2"></div>
            <div id="timer" class="mb-2"></div>
            <div id="date"></div>
        </div>

        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Grafik Penjualan</h2>
            <div class="w-full md:w-[500px] h-[300px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Data Produk</h2>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2 text-left">Nama Produk</th>
                        <th class="border p-2 text-left">Harga</th>
                        <th class="border p-2 text-left">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border p-2">Produk A</td>
                        <td class="border p-2">Rp 100.000</td>
                        <td class="border p-2">50</td>
                    </tr>
                    <tr>
                        <td class="border p-2">Produk B</td>
                        <td class="border p-2">Rp 150.000</td>
                        <td class="border p-2">30</td>
                    </tr>
                    <tr>
                        <td class="border p-2">Produk C</td>
                        <td class="border p-2">Rp 200.000</td>
                        <td class="border p-2">20</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    const salesChart = new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
            datasets: [{
                label: 'Penjualan',
                data: [12, 19, 3, 5, 2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString();
        document.getElementById('clock').textContent = 'Jam: ' + time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    let seconds = 0;

    function updateTimer() {
        seconds++;
        document.getElementById('timer').textContent = 'Timer: ' + seconds + ' detik';
    }
    setInterval(updateTimer, 1000);
    updateTimer();

    function updateDate() {
        const now = new Date();
        const date = now.toLocaleDateString();
        document.getElementById('date').textContent = 'Tanggal: ' + date;
    }
    updateDate();
    </script>
</body>

</html>