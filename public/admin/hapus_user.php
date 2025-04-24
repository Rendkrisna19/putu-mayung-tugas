<?php
session_start();
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    
    // Hapus data pengguna berdasarkan id
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Pengguna berhasil dihapus.";
    } else {
        $_SESSION['message'] = "Terjadi kesalahan: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: list_user.php");
exit();
?>