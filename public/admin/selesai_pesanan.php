<?php
session_start();
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Update status pesanan di tabel payments
    $stmt = $conn->prepare("UPDATE payments SET status = 'completed' WHERE id_order = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Redirect to the order queue page
    header("Location: antrian_pesanan.php");
    exit();
}
?>