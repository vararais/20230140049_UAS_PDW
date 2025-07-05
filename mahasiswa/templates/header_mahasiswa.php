<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$base_path = '/Anan-Simprak'; // Sesuaikan jika nama folder proyek Anda berbeda

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') { header("Location: " . $base_path . "/login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Mahasiswa - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body style="background-color: #F4EBD3;">

<nav style="background-color: #555879;" class="text-white shadow-lg">
    <div class="container mx-auto px-6 py-3 flex justify-between items-center">
        <a href="<?php echo $base_path; ?>/mahasiswa/dashboard.php" class="text-xl font-bold">Simprak Mahasiswa</a>
        <ul class="flex items-center space-x-1">
            <?php 
                // Style link aktif dan non-aktif baru
                $activeClass = 'bg-black/20 font-semibold';
                $inactiveClass = 'hover:bg-black/20 font-medium';
            ?>
            <li><a href="<?php echo $base_path; ?>/mahasiswa/dashboard.php" class="px-3 py-2 rounded-lg transition-colors text-sm <?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?>">Dashboard</a></li>
            <li><a href="<?php echo $base_path; ?>/katalog.php" class="px-3 py-2 rounded-lg transition-colors text-sm <?php echo ($activePage == 'katalog') ? $activeClass : $inactiveClass; ?>">Katalog Praktikum</a></li>
            <li><a href="<?php echo $base_path; ?>/mahasiswa/praktikum_saya.php" class="px-3 py-2 rounded-lg transition-colors text-sm <?php echo ($activePage == 'praktikum_saya') ? $activeClass : $inactiveClass; ?>">Praktikum Ku</a></li>
        </ul>
        <div class="flex items-center space-x-4">
            <span class="font-medium text-gray-300 hidden md:block text-sm">Mahasiswa : <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
            <a href="<?php echo $base_path; ?>/logout.php" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">Logout</a>
        </div>
    </div>
</nav>

<main class="container mx-auto p-6 lg:p-10">
    <h1 class="text-4xl font-extrabold" style="color: #555879;"><?php echo $pageTitle ?? 'Halaman'; ?></h1>
    ```
