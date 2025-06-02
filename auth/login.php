<?php
session_start();
include("../config/config.php");
include("../tailwind.html");
include("../backend/auth.php");
include("../components/SwetAllert.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Putumayung</title>
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    :root {
        font-family: 'Inter', sans-serif;
    }

    @supports (font-variation-settings: normal) {
        :root {
            font-family: 'Inter var', sans-serif;
        }
    }

    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }

    .gradient-bg {
        background: linear-gradient(135deg, #6366f1 0%, #60a5fa 100%);
    }

    .input-effect:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px #6366f1;
        transition: .2s;
    }

    .btn-animate {
        transition: transform .1s, box-shadow .1s;
    }

    .btn-animate:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 24px 0 rgba(99, 102, 241, 0.2);
    }
    </style>
</head>

<body class="antialiased gradient-bg min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full mx-auto my-10 glass p-10 rounded-2xl shadow-lg animate-fade-in">
        <div class="flex flex-col items-center mb-6">
            <img src="https://img.icons8.com/color/96/000000/lock--v1.png" class="mb-2 animate-bounce" alt="Login Icon">
            <h1 class="text-4xl font-bold text-indigo-700 mb-1">Login</h1>
            <p class="text-slate-600">Hi, Welcome back 👋</p>
        </div>
        <button
            class="w-full text-center py-3 mb-6 border flex space-x-2 items-center justify-center border-slate-200 rounded-lg text-slate-700 hover:border-indigo-400 hover:text-indigo-700 hover:shadow transition duration-150 btn-animate bg-white/80">
            <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-6 h-6" alt=""> <span>Login with
                Google</span>
        </button>
        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block font-medium text-slate-700 pb-2">Email address</label>
                <input id="email" name="email" type="email"
                    class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none input-effect bg-white/80"
                    placeholder="Enter email address" required>
            </div>
            <div>
                <label for="password" class="block font-medium text-slate-700 pb-2">Password</label>
                <input id="password" name="password" type="password"
                    class="w-full py-3 border border-slate-200 rounded-lg px-3 focus:outline-none input-effect bg-white/80"
                    placeholder="Enter your password" required>
            </div>
            <div class="flex flex-row justify-between items-center">
                <label for="remember" class="flex items-center space-x-2">
                    <input type="checkbox" id="remember" class="w-4 h-4 border-slate-200 focus:bg-indigo-600">
                    <span>Remember me</span>
                </label>
                <a href="#" class="font-medium text-indigo-600 hover:underline">Forgot Password?</a>
            </div>
            <button
                class="w-full py-3 font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-400 hover:from-indigo-500 hover:to-blue-500 rounded-lg border-none btn-animate inline-flex items-center justify-center space-x-2 shadow-lg">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-700" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>

                <span class="text-indigo-700">Login</span>
            </button>

            <p class="text-center mt-4">Not registered yet?
                <a href="register.php"
                    class="text-indigo-600 font-medium inline-flex space-x-1 items-center hover:underline">
                    <span>Register now</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </span>
                </a>
            </p>
        </form>
    </div>
    <script>
    // Fade-in animation
    document.querySelector('.animate-fade-in').style.opacity = 0;
    setTimeout(() => {
        document.querySelector('.animate-fade-in').style.transition = 'opacity 0.8s';
        document.querySelector('.animate-fade-in').style.opacity = 1;
    }, 100);
    </script>
</body>

</html>