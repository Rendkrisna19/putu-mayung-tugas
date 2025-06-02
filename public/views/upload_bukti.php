<?php
session_start();
include("../../config/config.php");

// Setelah login berhasil, pastikan di login:
// $_SESSION['user_id'] = $user_data['id']; // contoh penamaan konsisten user_id

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_order = $_POST['id_order'];
    $id_bank = $_POST['bank'];
    $nama_bank = $_POST['nama_bank'];

    // Handle file upload
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($_FILES["bukti_pembayaran"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["bukti_pembayaran"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO payments (id_order, id_bank, nama_bank, bukti_pembayaran, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iiss", $id_order, $id_bank, $nama_bank, $target_file);
            $stmt->execute();
            $stmt->close();

            echo "The file ". htmlspecialchars(basename($_FILES["bukti_pembayaran"]["name"])). " has been uploaded.";
            header("Location: success.php");
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!-- <html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Sedang Diproses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto my-10 p-6 bg-white shadow rounded text-center">
        <h1 class="text-2xl font-bold mb-6">Pesanan Kamu Sedang Diproses✅/h1>
            <p class="mb-4 text-gray-700">Harap tunggu ya, Sihlakan Hubungi Admin kami
                <a href="index.php"
                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Kembali
                    ke
                    Beranda</a>
    </div>
</body>

</html> -->