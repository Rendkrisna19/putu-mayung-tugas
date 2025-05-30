<?php
include("../config/config.php");
include("../tailwind.html");
include("../backend/auth.php");
include("../components/SwetAllert.php");



?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="antialiased bg-slate-200">
    <div class="max-w-lg mx-auto my-10 bg-white p-8 rounded-xl shadow shadow-slate-300">
        <h1 class="text-4xl font-medium">Register</h1>
        <p class="text-slate-500">Hi, Welcome back 👋</p>

        <div class="my-5">
            <button
                class="w-full text-center py-3 my-3 border flex space-x-2 items-center justify-center border-slate-200 rounded-lg text-slate-700 hover:border-slate-400 hover:text-slate-900 hover:shadow transition duration-150">
                <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-6 h-6" alt=""> <span>Login with
                    Google</span>
            </button>
        </div>
        <form method="POST" class="my-10 font-medium">
            <div class="flex flex-col space-y-5">
                <label>
                    <p class="font-medium text-slate-700 pb-2">Nama Lengkap</p>
                    <input name="name" type="text" required
                        class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow"
                        placeholder="Enter Full Your Name">
                </label>
                <label>
                    <p class="font-medium text-slate-700 pb-2">Username</p>
                    <input name="username" type="text" required
                        class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow"
                        placeholder="Enter Your Username">
                </label>
                <label>
                    <p class="font-medium text-slate-700 pb-2">Email address</p>
                    <input name="email" type="email" required
                        class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow"
                        placeholder="Enter email address">
                </label>
                <label>
                    <p class="font-medium text-slate-700 pb-2">Number Phone</p>
                    <input name="phone" type="number" required
                        class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow"
                        placeholder="Enter number phone">
                </label>
                <label>
                    <p class="font-medium text-slate-700 pb-2">Password</p>
                    <input name="password" type="password" required
                        class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow"
                        placeholder="Enter your password">
                </label>
                <label>
                    <p class="font-medium text-slate-700 pb-2">Ulangi Password</p>
                    <input name="confirm_password" type="password" required
                        class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none focus:border-slate-500 hover:shadow"
                        placeholder="Enter your password again">
                </label>

                <button type="submit"
                    class="w-full py-3 font-medium text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg border-indigo-500 hover:shadow inline-flex space-x-2 items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Register</span>
                </button>
                <p class="text-center">Sudah punya akun? <a href="login.php" class="text-indigo-600 font-medium">Login
                        di sini</a></p>
            </div>
        </form>
    </div>
</body>

</html>