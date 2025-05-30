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

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css" />

    <!-- Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <!-- Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

    <!-- Buttons HTML5 export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <!-- Buttons print -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- JSZip (untuk export Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- pdfmake (untuk export PDF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <style>
    /* Penyesuaian agar serasi dengan Tailwind */
    table.dataTable thead th {
        background-color: #f3f4f6;
        color: #952BFFFF;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 0.75rem 1rem;
        text-align: left;
    }

    table.dataTable tbody td {
        font-size: 0.875rem;
        color: #374151;
        padding: 0.75rem 1rem;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
        border-radius: 0.25rem;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        font-size: 0.75rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #863BF6FF;
        color: white !important;
        border: 1px solid #AE3BF6FF;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d5db;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
    }

    /* Cursor pointer for the expand icon */
    td.details-control {
        cursor: pointer;
    }

    /* Style for expand icon */
    tr.shown td.details-control::before {
        content: "▼";
    }

    td.details-control::before {
        content: ">";
        color: #AB3BF6FF;
        font-weight: bold;
        display: inline-block;
        margin-right: 6px;
    }
    </style>
</head>

<body class="bg-white min-h-screen text-gray-800">
    <div class="h-screen ">
        <!-- Sidebar -->
        <?php include("../../components/Slidebar.php"); ?>

        <div class="flex-grow p-6 transition-all duration-300">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-indigo-600">Daftar Order</h2>

                <div class="overflow-x-auto">
                    <table id="ordersTable" class="min-w-full w-full rounded-md text-blue-700">
                        <thead class="text-indigo-500">
                            <tr>
                                <th></th> <!-- Kolom untuk tombol expand -->
                                <th>ID</th>
                                <th>Alamat</th>
                                <th>Ongkir</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orders->fetch_assoc()) : ?>
                            <tr>
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
        </div>
    </div>

    <script>
    // Format detail produk yang akan ditampilkan di child row
    function format(orderId) {
        const orderDetails = <?= json_encode($orderDetails); ?>;

        if (!orderDetails[orderId]) {
            return '<em>Tidak ada produk</em>';
        }

        let html = '<ul class="list-disc ml-6 mt-1 text-gray-700">';
        orderDetails[orderId].forEach(item => {
            html += `<li>${item.nama_product} (${item.jumlah})</li>`;
        });
        html += '</ul>';

        return '<strong>Produk Dipesan:</strong> ' + html;
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

            // Tambahkan tombol export & print
            dom: 'Bfrtip', // letakkan tombol di atas tabel
            buttons: [
                'excelHtml5',
                'pdfHtml5',
                'print'
            ],
        });

        // Add event listener for opening and closing details
        $('#ordersTable tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // Close row
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open row
                var orderId = row.data()[1]; // kolom kedua adalah id
                row.child(format(orderId)).show();
                tr.addClass('shown');
            }
        });
    });
    </script>
</body>

</html>