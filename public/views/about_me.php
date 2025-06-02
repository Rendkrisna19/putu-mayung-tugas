<?php 
session_start();
include ("../../components/Navbar.php");

include("../../config/config.php");

// Setelah login berhasil, pastikan di login:
// $_SESSION['user_id'] = $user_data['id']; // contoh penamaan konsisten user_id

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Team</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


.font-global {
    font-family: "Poppins", sans-serif;
    font-weight: 400;
    font-style: normal;
}
</style>

<body>

    <body class="bg-white font-global">
        <section class="py-20 px-4">
            <div class="container mx-auto max-w-7xl">
                <!-- Section Header -->

                <h1 class="text-center text-bold text-indigo-600 mb-8 text-xl font-bold">Anggota Kelompok</h1>


                <!-- Team Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
                    <!-- Team Member 1 -->
                    <div class="group">
                        <div class="relative overflow-hidden rounded-xl mb-4">
                            <img src="../images/rendy.jpg" alt="Team member"
                                class="w-full aspect-[3/4] object-cover object-center transform group-hover:scale-105 transition duration-300 ease-in-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-800">Muhammad Rendy Krisna</h3>
                            <p class="text-indigo-600 font-medium">Developer</p>
                            <p class="text-gray-600 mt-2">2305102004</p>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="group">
                        <div class="relative overflow-hidden rounded-xl mb-4">
                            <img src="../images/mifdhal.jpg" alt="Team member"
                                class="w-full aspect-[3/4] object-cover object-center transform group-hover:scale-105 transition duration-300 ease-in-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-800">Muhammad Mifdhal Harahap</h3>
                            <p class="text-indigo-600 font-medium">Perancangan Sistem</p>
                            <p class="text-gray-600 mt-2">2305102019</p>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="group">
                        <div class="relative overflow-hidden rounded-xl mb-4">
                            <img src="../images/bima.jpg" alt=" Team member"
                                class="w-full aspect-[3/4] object-cover object-center transform group-hover:scale-105 transition duration-300 ease-in-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-800">Bima Sakti</h3>
                            <p class="text-indigo-600 font-medium">Perancangan DFD</p>
                            <p class="text-gray-600 mt-2">230510200.</p>
                        </div>
                    </div>
                    <!-- Team Member 3 -->
                    <div class="group">
                        <div class="relative overflow-hidden rounded-xl mb-4">
                            <img src="../images/ruth.jpg" alt=" Team member"
                                class="w-full aspect-[3/4] object-cover object-center transform group-hover:scale-105 transition duration-300 ease-in-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-800">Rut Saragih</h3>
                            <p class="text-indigo-600 font-medium">Analisis modul & masalah</p>
                            <p class="text-gray-600 mt-2">230510200.</p>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="group">
                        <div class="relative overflow-hidden rounded-xl mb-4">
                            <img src="../images/najwa.jpg" alt="Team member"
                                class="w-full aspect-[3/4] object-cover object-center transform group-hover:scale-105 transition duration-300 ease-in-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#"
                                        class="bg-white text-indigo-600 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-800">Najwa Siti Aulia</h3>
                            <p class="text-indigo-600 font-medium">Analisis modul & masalah</p>
                            <p class="text-gray-600 mt-2">230510200.</p>
                        </div>
                    </div>
                </div>


            </div>
        </section>
    </body>
</body>

</html>