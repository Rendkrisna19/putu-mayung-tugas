<?php
session_start();
include("../../config/config.php");
// include("../../components/Navbar.php");

// Cek jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = $_POST['name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

    if ($password !== $confirm) {
        echo "<script>alert('Password tidak sama!');</script>";
    } else {
        $foto = $user['foto'];

        if ($_FILES['foto']['name']) {
            $target_dir = "../../uploads/";
            $foto_name = basename($_FILES["foto"]["name"]);
            $target_file = $target_dir . time() . "_" . $foto_name;

            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = $target_file;
            }
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, username=?, password=?, foto=? WHERE id=?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $username, $hashedPassword, $foto, $user_id);
        $stmt->execute();

        echo "<script>alert('Profile berhasil diperbarui!'); window.location.href='edit_profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <style>
    :root {
        --purple: #6c5ce7;
        --white: #ffffff;
        --bg-light: #f8f8ff;
        --text-dark: #2d3436;
    }


    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: var(--bg-light);
        font-family: 'Segoe UI', sans-serif;
        color: var(--text-dark);
        padding: 0 20px 50px;
    }

    .container {
        max-width: 500px;
        margin: auto;
        background-color: var(--white);
        padding: 30px;
        border-radius: 12px;
        margin-top: 100px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .profile-pic-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin: auto;
        margin-bottom: 20px;
    }

    .profile-pic-wrapper img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--purple);
    }

    .edit-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: var(--purple);
        color: white;
        border-radius: 50%;
        padding: 8px;
        cursor: pointer;
    }

    .edit-icon:hover {
        background-color: #5a4bcf;
    }

    input[type="file"] {
        display: none;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
    }

    button {
        margin-top: 20px;
        width: 100%;
        background-color: var(--purple);
        color: white;
        border: none;
        padding: 12px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    button:hover {
        background-color: #5a4bcf;
    }

    .back-btn {
        text-align: center;
        margin-top: 15px;
    }

    .back-btn a {
        color: var(--purple);
        text-decoration: none;
        font-weight: bold;
    }

    .back-btn a:hover {
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .container {
            margin-top: 60px;
            padding: 20px;
        }

        .profile-pic-wrapper {
            width: 100px;
            height: 100px;
        }

        .profile-pic-wrapper img {
            width: 100px;
            height: 100px;
        }

        .edit-icon {
            padding: 6px;
        }


    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


    .font-global {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: normal;
    }
    </style>
</head>

<body class="font-global bg-white">

    <div class="container">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="profile-pic-wrapper">
                <img src="<?= $user['foto'] ? $user['foto'] : '../../assets/default-user.png' ?>" alt="Foto Profil">
                <label class="edit-icon" for="foto">&#9998;</label>
                <input type="file" name="foto" id="foto" accept="image/*">
            </div>

            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="phone">Nomor HP</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

            <label for="username">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">

            <label for="password">Password Baru</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Konfirmasi Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Simpan Perubahan</button>

            <div class="back-btn">
                <a href="../views/index.php">Back</a>
            </div>
        </form>
    </div>

    <script>
    // Preview upload image secara langsung (opsional)
    document.getElementById("foto").addEventListener("change", function(event) {
        const img = document.querySelector(".profile-pic-wrapper img");
        img.src = URL.createObjectURL(event.target.files[0]);
    });
    </script>

</body>

</html>