<?php
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Misalnya update status order
    $sql = "UPDATE order_items SET status = 'confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        header("Location: daftar_pesanan.php?success=1");
        exit();
    } else {
        echo "Gagal mengkonfirmasi pesanan.";
    }

    $stmt->close();
    $conn->close();
}
?>
<?php
session_start();
include("../../config/config.php");

// Fetch orders from the database
$sql = "SELECT order_items.id AS order_id, products.nama_product AS product_name, order_items.jumlah, payments.bukti_pembayaran 
    FROM order_items 
    JOIN products ON order_items.id_product = products.id_product
    LEFT JOIN payments ON order_items.id_order = payments.id_order";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-somehashhere" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


    .font-global {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: normal;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    </style>


</head>

<body class="bg-white font-global">
    <div class="h-screen">
        <!-- Sidebar -->
        <?php include('../../components/Slidebar.php'); ?>

        <!-- Konten Utama -->
        <div class="flex-grow p-6 transition-all duration-300">
            <h1 class="text-3xl font-bold mb-6 text-indigo-600">Daftar Pesanan</h1>
            <div class="bg-white p-4 rounded shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID Pesanan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bukti Pembayaran
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='px-4 py-2'>" . $row["order_id"] . "</td>";
                                echo "<td class='px-4 py-2'>" . $row["product_name"] . "</td>";
                                echo "<td class='px-4 py-2'>" . $row["jumlah"] . "</td>";
                                echo "<td class='px-4 py-2'>";
                                if (!empty($row["bukti_pembayaran"])) {
                                    echo "<img src='../uploads/" . $row["bukti_pembayaran"] . "' alt='Bukti Pembayaran' class='w-16 h-16 object-cover rounded cursor-pointer' onclick='openModal(this.src)'>";
                                } else {
                                    echo "<span class='text-gray-500'>No Bukti</span>";
                                }
                                echo "</td>";
                                echo "<td class='px-4 py-2'>";
                                if (!empty($row["bukti_pembayaran"])) {
                                    echo "<form action='konfirmasi_pembayaran.php' method='POST'>";
                                    echo "<input type='hidden' name='order_id' value='" . $row["order_id"] . "'>";
                                    echo "<button type='submit' class='bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition'>Konfirmasi</button>";
                                    echo "</form>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-4 py-2 text-center text-gray-500'>No orders found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Image -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modalImage" src="" alt="Bukti Pembayaran" class="w-full h-auto">
        </div>
    </div>

    <script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Function to open the modal
    function openModal(src) {
        document.getElementById("modalImage").src = src;
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>

</html>