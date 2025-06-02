<?php
include("../config/config.php");
include("../tailwind.html");
include("../backend/auth.php");
include("../components/SwetAllert.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .fade-in-up {
        opacity: 0;
        transform: translateY(40px);
        animation: fadeInUp 0.8s cubic-bezier(.4, 0, .2, 1) forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
    }
    </style>
</head>

<body
    class="antialiased bg-gradient-to-br from-indigo-200 via-slate-200 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full mx-auto my-10 glass p-10 rounded-2xl shadow-2xl fade-in-up border border-indigo-100">
        <h1 class="text-4xl font-bold text-indigo-700 mb-2 text-center tracking-tight">Register</h1>
        <p class="text-slate-500 text-center mb-6">Hi, Welcome! Silakan daftar akun baru 👋</p>

        <div class="my-5">
            <button
                class="w-full text-center py-3 my-3 border flex space-x-2 items-center justify-center border-slate-200 rounded-lg text-slate-700 hover:border-indigo-400 hover:text-indigo-700 hover:shadow-lg transition duration-200 bg-white">
                <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-6 h-6" alt=""> <span>Login with
                    Google</span>
            </button>
        </div>
        <form method="POST" class="my-8 font-medium space-y-5">
            <label>
                <p class="font-medium text-slate-700 pb-2">Nama Lengkap</p>
                <input name="name" type="text" required
                    class="w-full py-3 border border-slate-200 rounded-xl px-3 focus:outline-none focus:border-indigo-500 hover:shadow transition"
                    placeholder="Enter Full Your Name">
            </label>
            <label>
                <p class="font-medium text-slate-700 pb-2">Username</p>
                <input name="username" type="text" required
                    class="w-full py-3 border border-slate-200 rounded-xl px-3 focus:outline-none focus:border-indigo-500 hover:shadow transition"
                    placeholder="Enter Your Username">
            </label>
            <label>
                <p class="font-medium text-slate-700 pb-2">Email address</p>
                <input name="email" type="email" required
                    class="w-full py-3 border border-slate-200 rounded-xl px-3 focus:outline-none focus:border-indigo-500 hover:shadow transition"
                    placeholder="Enter email address">
            </label>
            <label>
                <p class="font-medium text-slate-700 pb-2">Number Phone</p>
                <input name="phone" type="number" required
                    class="w-full py-3 border border-slate-200 rounded-xl px-3 focus:outline-none focus:border-indigo-500 hover:shadow transition"
                    placeholder="Enter number phone">
            </label>
            <label>
                <p class="font-medium text-slate-700 pb-2">Password</p>
                <input name="password" type="password" required
                    class="w-full py-3 border border-slate-200 rounded-xl px-3 focus:outline-none focus:border-indigo-500 hover:shadow transition"
                    placeholder="Enter your password">
            </label>
            <label>
                <p class="font-medium text-slate-700 pb-2">Ulangi Password</p>
                <input name="confirm_password" type="password" required
                    class="w-full py-3 border border-slate-200 rounded-xl px-3 focus:outline-none focus:border-indigo-500 hover:shadow transition"
                    placeholder="Enter your password again">
            </label>

            <button type="submit"
                class="w-full py-3 font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 rounded-xl shadow-lg hover:shadow-xl transition flex items-center justify-center space-x-2 text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>Register</span>
            </button>
            <p class="text-center mt-2">Sudah punya akun? <a href="login.php"
                    class="text-indigo-600 font-semibold hover:underline">Login di sini</a></p>
        </form>
    </div>
</body>

</html>