<?php

include("../../config/config.php");

$user_id = $_SESSION["user_id"] ?? null;
$name = "Guest";
$avatar = "https://i.pravatar.cc/100?u=guest"; // default avatar

if ($user_id) {
    $sql = "SELECT name, foto FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['foto'])) {
$avatar = "../uploads/" . htmlspecialchars($row['foto']);        }
        if (!empty($row['name'])) {
            $name = htmlspecialchars($row['name']);
        }
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<!-- Tambahkan ini di bagian <head> -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:ital,wght@0,600;0,700;0,800;1,500;1,700;1,800&display=swap');

.text-modify {
    font-family: "Pacifico", cursive;
    font-weight: 100;
    font-style: normal;
}
</style>

<nav class="bg-indigo-600 text-white p-4 fixed w-full z-50 shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Left: Logo & Brand -->
        <div class="flex items-center space-x-2">
            <img src="../images/Mayang Hids.png" alt="Logo" class="h-10 w-10">
            <span class="text-xl font-bold text-modify">Mayang <span class="text-orange-700">Hids</span></span>
        </div>

        <!-- Center: Menu (Desktop) -->
        <div class="hidden md:flex space-x-4">
            <a href="../views/index.php" class="hover:text-gray-300 nav-link">Home</a>
            <a href="../views/about_me.php" class="hover:text-gray-300 nav-link">About Us</a>
            <a href="../views/contact.php" class="hover:text-gray-300 nav-link">Contact</a>
            <a href="../views/testimoni_pelanggan.php" class="hover:text-gray-300 nav-link">Testimoni</a>

        </div>

        <!-- Right: Icons & Profile  ../views/checkout.php route eror kan untuk chekout -->
        <div class="flex items-center space-x-4">
            <a href="../views/checkout.php" class="relative hover:text-gray-300">
                <i class="fas fa-shopping-cart text-xl"></i>
                <!-- Notification for checkout count -->
                <span id="checkoutCount"
                    class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full hidden">
                    0
                </span>
            </a>

            <div class="relative">
                <button onclick="toggleDropdown()" class="focus:outline-none">
                    <img src="<?= $avatar ?>" alt="Avatar" class="w-10 h-10 rounded-full mr-3 object-cover">
                </button>

                <div id="profileDropdown"
                    class="hidden absolute right-0 mt-2 w-48 bg-white text-black rounded shadow-lg z-50">


                    <!-- Bagian atas dengan avatar dan nama -->
                    <div class="flex items-center px-4 py-3 border-b border-gray-200">
                        <img src="<?= $avatar ?>" alt="Avatar" class="w-10 h-10 rounded-full mr-3 object-cover">
                        <div>
                            <p class="font-semibold text-sm"><?= $name ?></p> <a href="../views/edit_profile.php"
                                class="text-xs text-blue-500 hover:underline">Edit
                                Profile</a>
                        </div>
                    </div>


                    <a href="../views/status_pesanan.php" class="block px-4 py-2 hover:bg-gray-100">Status Pesanan
                        saya</a>

                    <!-- Tombol logout -->
                    <form action="../../auth/login.php" method="POST">
                        <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                    </form>
                </div>

            </div>

            <!-- Hamburger Icon (Mobile) -->
            <button id="menu-btn" class="md:hidden focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-indigo-800 p-4 mt-4 rounded-lg shadow-lg">
        <a href="../views/index.php" class="block py-2 nav-link">Home</a>
        <a href="../views/about_me.php" class="block py-2 nav-link">About</a>
        <a href="../views/contact.php" class="block py-2 nav-link">Contact</a>
        <a href="../views/testimoni_pelanggan.php" class="block py-2 nav-link">Testimoni</a>
    </div>
</nav>

<script>
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
let menuOpen = false;

menuBtn.addEventListener('click', () => {
    if (!menuOpen) {
        menuBtn.innerHTML = '<i class="fas fa-times text-xl"></i>';
        mobileMenu.classList.remove('hidden');
        menuOpen = true;
    } else {
        menuBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
        mobileMenu.classList.add('hidden');
        menuOpen = false;
    }
});

// Active state for nav link
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('text-blue-400'));
        this.classList.add('text-blue-400');
    });
});

// Fetch checkout data
document.addEventListener("DOMContentLoaded", function() {
    fetch("../views/get_checkout_count.php")
        .then(response => response.json())
        .then(data => {
            const checkoutCount = document.getElementById("checkoutCount");
            if (data.count > 0) {
                checkoutCount.textContent = data.count;
                checkoutCount.classList.remove("hidden"); // Show notification
            }
        })
        .catch(error => console.error("Error fetching checkout count:", error));
});

//hover Navbar Dropdown --
function toggleDropdown() {
    const dropdown = document.getElementById("profileDropdown");
    dropdown.classList.toggle("hidden");
}

// Opsional: tutup dropdown saat klik di luar
document.addEventListener('click', function(event) {
    const isClickInside = event.target.closest('.relative');
    if (!isClickInside) {
        document.getElementById('profileDropdown').classList.add('hidden');
    }
});
</script>