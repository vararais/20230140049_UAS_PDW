<?php
// PHP LOGIC (atas) TETAP SAMA SEPERTI ASLINYA, TIDAK ADA PERUBAHAN.
require_once 'config.php';
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($nama) || empty($email) || empty($password) || empty($role)) { $message = "Semua field harus diisi!"; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $message = "Format email tidak valid!"; }
    elseif (!in_array($role, ['mahasiswa', 'asisten'])) { $message = "Peran tidak valid!"; }
    else {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) { $message = "Email sudah terdaftar. Silakan gunakan email lain."; } 
        else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_insert = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);
            if ($stmt_insert->execute()) { header("Location: login.php?status=registered"); exit(); } 
            else { $message = "Terjadi kesalahan. Silakan coba lagi."; }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
if(isset($conn)) { $conn->close(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - SIMPRAK</title>
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
                    <h2 class="text-3xl font-extrabold text-gray-800">Buat Akun Baru</h2>
                    <p class="text-gray-500 mt-2">Ayo bergabung dengan SIMPRAK!</p>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="mb-4 p-3 rounded-lg border border-red-200 bg-red-50 text-red-800 font-medium text-center text-sm"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form action="register.php" method="post" class="space-y-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Daftar Sebagai</label>
                        <select id="role" name="role" class="mt-1 w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="asisten">Asisten</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-all text-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Buat Akun
                    </button>
                </form>
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">Sudah punya akun? <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Login di sini</a></p>
                </div>
            </div>
             <div class="text-center mt-6">
                 <a href="katalog.php" class="text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke Katalog</a>
            </div>
        </div>
    </div>
</body>
</html>