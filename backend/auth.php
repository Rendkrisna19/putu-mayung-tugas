<?php


echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cek apakah form login atau register berdasarkan input yang dikirim
    if (isset($_POST["email"]) && isset($_POST["password"]) && !isset($_POST["name"])) {
        // ==== LOGIN ====
        $email = $_POST["email"];
        $password = $_POST["password"];

        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

       if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user"] = $user;
    $_SESSION["user_id"] = $user["id"]; // ✅ Tambahkan baris ini
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil',
                    text: 'Selamat datang, " . htmlspecialchars($user["name"]) . "!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '../public/views/index.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'Email atau password salah!'
                });
            </script>";
        }

        $stmt->close();
    }

    elseif (
        isset($_POST["name"]) && isset($_POST["username"]) &&
        isset($_POST["email"]) && isset($_POST["phone"]) &&
        isset($_POST["password"]) && isset($_POST["confirm_password"])
    ) {
        // ==== REGISTER ====
        $name = $_POST["name"];
        $username = $_POST["username"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $query = "INSERT INTO users (name, username, email, phone, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $name, $username, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil',
                        text: 'Akan dialihkan ke halaman login...',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Register',
                        text: 'Terjadi kesalahan saat menyimpan data.'
                    });
                </script>";
            }

            $stmt->close();
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Tidak Cocok',
                    text: 'Silakan periksa ulang password Anda.'
                });
            </script>";
        }
    } else {
        // Data tidak lengkap (fallback)
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Lengkap',
                text: 'Mohon isi semua field yang diperlukan.'
            });
        </script>";
    }

    $conn->close();
}
?>