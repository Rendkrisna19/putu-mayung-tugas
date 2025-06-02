<?php
include '../../config/config.php'; // Sesuaikan dengan koneksi database

session_start();
include("../../config/config.php");

// Setelah login berhasil, pastikan di login:
// $_SESSION['user_id'] = $user_data['id']; // contoh penamaan konsisten user_id

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}


$query = "SELECT COUNT(*) AS total FROM checkout WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["count" => $row['total']]);
?>