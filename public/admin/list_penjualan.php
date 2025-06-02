<?php
session_start();
include(__DIR__ . "/../../config/config.php");

if (!isset($_SESSION["admin"])) {
    header("Location: ../../auth/admin/auth.php");
    exit;
}

// Ambil semua order
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");

// Ambil semua detail item dengan nama produk
$orderDetails = [];
$queryDetails = "
    SELECT oi.id_order, p.nama_product, oi.jumlah
    FROM order_items oi
    JOIN products p ON oi.id_product = p.id_product
";
$detailsResult = $conn->query($queryDetails);

while ($detail = $detailsResult->fetch_assoc()) {
    $orderDetails[$detail['id_order']][] = [
        'nama_product' => $detail['nama_product'],
        'jumlah' => $detail['jumlah']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Daftar Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#7C3AED',
                    secondary: '#F3F4F6',
                    accent: '#F59E42',
                    dark: '#18181B',
                    light: '#F9FAFB'
                },
                fontFamily: {
                    poppins: ['Poppins', 'sans-serif']
                }
            }
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f3f4f6 0%, #e0e7ff 100%);
        min-height: 100vh;
    }

    .glass {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        backdrop-filter: blur(8px);
        border-radius: 1.5rem;
    }

    table.dataTable thead th {
        background: #ede9fe;
        color: #7C3AED;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #a78bfa;
    }

    table.dataTable tbody td {
        font-size: 1rem;
        color: #18181B;
        padding: 1rem 1.25rem;
        background: transparent;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 1rem;
        margin: 0 4px;
        border-radius: 0.5rem;
        background: #ede9fe;
        border: 1px solid #a78bfa;
        font-size: 0.85rem;
        color: #7C3AED !important;
        transition: background 0.2s;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #7C3AED;
        color: white !important;
        border: 1px solid #7C3AED;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #a78bfa;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        background: #f3f4f6;
        color: #7C3AED;
    }

    td.details-control {
        cursor: pointer;
        text-align: center;
    }

    tr.shown td.details-control::before {
        content: "▼";
        color: #7C3AED;
        font-size: 1.2rem;
    }

    td.details-control::before {
        content: "▶";
        color: #7C3AED;
        font-size: 1.2rem;
        font-weight: bold;
        display: inline-block;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        background: #ede9fe;
    }

    ::-webkit-scrollbar-thumb {
        background: #a78bfa;
        border-radius: 8px;
    }

    /* Responsive tweaks */
    @media (max-width: 640px) {
        .glass {
            padding: 1rem !important;
        }

        table.dataTable thead th,
        table.dataTable tbody td {
            padding: 0.5rem !important;
        }
    }
    </style>
</head>

<body class="bg-gradient-to-br from-secondary to-light min-h-screen text-dark">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <?php include("../../components/Slidebar.php"); ?>

        <main class="flex-1 flex flex-col items-center justify-center py-10 px-2 sm:px-8 bg-transparent">
            <div class="glass w-full max-w-6xl mx-auto p-8 shadow-xl">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-primary tracking-tight">Daftar Order</h2>
                        <p class="text-gray-500 mt-1">Kelola dan pantau semua transaksi penjualan dengan tampilan
                            modern.</p>
                    </div>
                    <!-- <div>
                        <a href="tambah_order.php"
                            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-indigo-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Order
                        </a>
                    </div> -->
                </div>
                <div class="overflow-x-auto rounded-lg border border-secondary shadow">
                    <table id="ordersTable" class="min-w-full w-full rounded-lg text-dark bg-white">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Alamat</th>
                                <th>Ongkir</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orders->fetch_assoc()) : ?>
                            <tr class="hover:bg-secondary transition">
                                <td class="details-control"></td>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td>Rp <?= number_format($row['ongkir']) ?></td>
                                <td>Rp <?= number_format($row['total_harga']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Format detail produk yang akan ditampilkan di child row
    function format(orderId) {
        const orderDetails = <?= json_encode($orderDetails); ?>;

        if (!orderDetails[orderId]) {
            return '<em class="text-gray-400">Tidak ada produk</em>';
        }

        let html = '<ul class="list-disc ml-6 mt-2 text-gray-700">';
        orderDetails[orderId].forEach(item => {
            html +=
                `<li class="mb-1"><span class="font-semibold text-primary">${item.nama_product}</span> <span class="bg-secondary px-2 py-0.5 rounded text-xs ml-2">x${item.jumlah}</span></li>`;
        });
        html += '</ul>';

        return '<div class="py-2 px-4 bg-secondary rounded-lg"><span class="font-semibold text-primary">Produk Dipesan:</span>' +
            html + '</div>';
    }

    $(document).ready(function() {
        var table = $('#ordersTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Data tidak ditemukan",
            },
            columnDefs: [{
                className: 'details-control',
                orderable: false,
                targets: 0
            }],
            order: [
                [1, 'desc']
            ],
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"Bf>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4"lip>',
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'bg-primary text-white px-4 py-2 rounded-lg mr-2 mb-2 hover:bg-indigo-700 transition',
                    text: '<svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m-4-5v9"/></svg> Excel'
                },
                {
                    extend: 'pdfHtml5',
                    className: 'bg-accent text-white px-4 py-2 rounded-lg mr-2 mb-2 hover:bg-orange-500 transition',
                    text: '<svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> PDF'
                },
                {
                    extend: 'print',
                    className: 'bg-dark text-white px-4 py-2 rounded-lg mb-2 hover:bg-gray-900 transition',
                    text: '<svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9v12h12V9M6 9V7a2 2 0 012-2h8a2 2 0 012 2v2M6 9h12"/></svg> Print'
                }
            ],
        });

        // Add event listener for opening and closing details
        $('#ordersTable tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                var orderId = row.data()[1];
                row.child(format(orderId)).show();
                tr.addClass('shown');
            }
        });
    });
    </script>
</body>

</html>