<?php
session_start();
include("../../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Ambil dan sanitasi input dari form
    $id       = (int) $_POST['id'];
    $email    = $conn->real_escape_string($_POST['email']);
    $phone    = $conn->real_escape_string($_POST['phone']);
    $name     = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
    // Jika ada validasi lain, tambahkan di sini
    // Misal: panjang minimal password, validasi email, dsb.
    
    // Update data pengguna di database
    $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, name = ?, username = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $email, $phone, $name, $username, $password, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Data pengguna berhasil diperbarui.";
    } else {
        $_SESSION['message'] = "Terjadi kesalahan: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: list_user.php");
exit();
?>