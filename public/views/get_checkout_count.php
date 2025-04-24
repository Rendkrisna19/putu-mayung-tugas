<?php
include '../../config/config.php'; // Sesuaikan dengan koneksi database

session_start();
$user_id = $_SESSION['user_id']; // Pastikan user sudah login

$query = "SELECT COUNT(*) AS total FROM checkout WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["count" => $row['total']]);
?>