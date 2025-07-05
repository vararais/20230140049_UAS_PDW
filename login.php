<?php
// PHP LOGIC (atas) TETAP SAMA SEPERTI ASLINYA, TIDAK ADA PERUBAHAN.
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'asisten') { header("Location: asisten/dashboard.php"); } 
    elseif ($_SESSION['role'] == 'mahasiswa') { header("Location: mahasiswa/dashboard.php"); }
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'asisten') { header("Location: asisten/dashboard.php"); } 
                elseif ($user['role'] == 'mahasiswa') { header("Location: mahasiswa/dashboard.php"); }
                exit();
            } else { $message = "Password yang Anda masukkan salah."; }
        } else { $message = "Akun dengan email tersebut tidak ditemukan."; }
        $stmt->close();
    }
}
if(isset($conn)) { $conn->close(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body {font-family: 'Inter', sans-serif;}</style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="bg-white p-8 rounded-xl shadow-xl">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-800">Selamat Datang</h2>
                    <p class="text-gray-500 mt-2">Masuk untuk melanjutkan ke dashboard Anda.</p>
                </div>
                
                <?php 
                    if (isset($_GET['status']) && $_GET['status'] == 'registered') {
                        echo '<div class="mb-4 p-3 rounded-lg border border-green-200 bg-green-50 text-green-800 font-medium text-center text-sm">Registrasi berhasil! Silakan login.</div>';
                    }
                    if (isset($_GET['status']) && $_GET['status'] == 'logout') {
                        echo '<div class="mb-4 p-3 rounded-lg border border-blue-200 bg-blue-50 text-blue-800 font-medium text-center text-sm">Anda telah logout. Silakan login kembali.</div>';
                    }
                    if (!empty($message)) {
                        echo '<div class="mb-4 p-3 rounded-lg border border-red-200 bg-red-50 text-red-800 font-medium text-center text-sm">' . htmlspecialchars($message) . '</div>';
                    }
                ?>

                <form action="login.php" method="post" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-all text-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Masuk Sekarang
                    </button>
                </form>
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">Belum punya akun? <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">Daftar di sini</a></p>
                </div>
            </div>
            <div class="text-center mt-6">
                 <a href="katalog.php" class="text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke Katalog</a>
            </div>
        </div>
    </div>
</body>
</html>