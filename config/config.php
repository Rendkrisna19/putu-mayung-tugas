<?php
$host = "localhost";
$user = "root"; // Sesuaikan dengan user MySQL
$pass = ""; // Sesuaikan dengan password MySQL
$dbname = "putumayung";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>