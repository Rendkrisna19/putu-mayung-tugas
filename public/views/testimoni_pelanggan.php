<?php 
session_start();
include ("../../components/Navbar.php");
include("../../config/config.php");

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Pelanggan</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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

    <body class="bg-white font-global ">
        <!-- Navbar Static Top -->
        <div class="w-full fixed top-0 left-0 z-50">
            <?php include("../../components/Navbar.php"); ?>
        </div>
        <div class="container mx-auto max-w-4xl px-4 py-28">
            <h1 class="text-3xl md:text-4xl font-bold text-indigo-700 text-center mb-2 tracking-wide">Testimoni
                Pelanggan
            </h1>
            <div class="text-center text-indigo-500 mb-10">Apa kata mereka tentang layanan kami?</div>
            <div class="flex flex-wrap gap-6 justify-center">
                <div
                    class="bg-gradient-to-br from-indigo-700 to-indigo-400 text-white rounded-xl shadow-lg p-7 max-w-xs flex flex-col items-center transition-transform duration-200 hover:-translate-y-2 hover:scale-105 hover:shadow-2xl">
                    <img class="w-20 h-20 rounded-full object-cover border-4 border-white mb-4 shadow"
                        src="https://randomuser.me/api/portraits/men/32.jpg" alt="Pelanggan 1">
                    <div class="font-bold text-lg mb-1">Andi Saputra</div>
                    <div class="text-indigo-200 text-sm mb-4">Pengusaha</div>
                    <div class="text-center text-indigo-50">"Pelayanan sangat ramah dan cepat. Saya sangat puas dengan
                        hasilnya, sangat direkomendasikan!"</div>
                </div>
                <div
                    class="bg-gradient-to-br from-indigo-700 to-indigo-400 text-white rounded-xl shadow-lg p-7 max-w-xs flex flex-col items-center transition-transform duration-200 hover:-translate-y-2 hover:scale-105 hover:shadow-2xl">
                    <img class="w-20 h-20 rounded-full object-cover border-4 border-white mb-4 shadow"
                        src="https://randomuser.me/api/portraits/women/44.jpg" alt="Pelanggan 2">
                    <div class="font-bold text-lg mb-1">Siti Rahmawati</div>
                    <div class="text-indigo-200 text-sm mb-4">Ibu Rumah Tangga</div>
                    <div class="text-center text-indigo-50">"Desain modern dan proses pemesanan mudah. Saya pasti akan
                        kembali
                        lagi!"</div>
                </div>
                <div
                    class="bg-gradient-to-br from-indigo-700 to-indigo-400 text-white rounded-xl shadow-lg p-7 max-w-xs flex flex-col items-center transition-transform duration-200 hover:-translate-y-2 hover:scale-105 hover:shadow-2xl">
                    <img class="w-20 h-20 rounded-full object-cover border-4 border-white mb-4 shadow"
                        src="https://randomuser.me/api/portraits/men/65.jpg" alt="Pelanggan 3">
                    <div class="font-bold text-lg mb-1">Budi Santoso</div>
                    <div class="text-indigo-200 text-sm mb-4">Freelancer</div>
                    <div class="text-center text-indigo-50">"Hasil kerja sangat memuaskan, sesuai dengan ekspektasi
                        saya.
                        Terima kasih atas pelayanannya!"</div>
                </div>
                <div
                    class="bg-gradient-to-br from-indigo-700 to-indigo-400 text-white rounded-xl shadow-lg p-7 max-w-xs flex flex-col items-center transition-transform duration-200 hover:-translate-y-2 hover:scale-105 hover:shadow-2xl">
                    <img class="w-20 h-20 rounded-full object-cover border-4 border-white mb-4 shadow"
                        src="https://randomuser.me/api/portraits/women/68.jpg" alt="Pelanggan 4">
                    <div class="font-bold text-lg mb-1">Dewi Lestari</div>
                    <div class="text-indigo-200 text-sm mb-4">Mahasiswa</div>
                    <div class="text-center text-indigo-50">"Sangat profesional dan responsif. Saya merasa nyaman
                        bertransaksi
                        di sini."</div>
                </div>
            </div>
        </div>
        <?php 
  include ("../../components/Footer.php");?>
    </body>

</html>