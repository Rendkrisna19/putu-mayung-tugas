<?php 
session_start();
include("../../config/config.php");
// include("../../components/Navbar.php");

// Cek jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

include ("../../components/Navbar.php");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Putu Mayung Shop</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="//code.tidio.co/jafhfsczgadnh4w7jc6yzhcozjch5eyt.js" async></script>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


.font-global {
    font-family: "Poppins", sans-serif;
    font-weight: 400;
    font-style: normal;
}
</style>
<script src="//code.tidio.co/jafhfsczgadnh4w7jc6yzhcozjch5eyt.js" async></script>

<body class="font-global">
    <?php 
    include ("../../components/HeroSection.php");
    include ("product.php");
    include ("features.php");
    include ("../../components/Footer.php");
    
    ?>
</body>

</html>