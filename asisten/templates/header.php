<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$base_path = '/Anan-Simprak'; // Sesuaikan jika nama folder proyek Anda berbeda

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') { header("Location: " . $base_path . "/login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body style="background-color: #EBFFD8;">

<nav style="background-color: #8DBCC7;" class="text-white shadow-lg">
    <div class="container mx-auto px-6 py-3 flex justify-between items-center">
        <a href="<?php echo $base_path; ?>/asisten/dashboard.php" class="text-xl font-bold text-gray-800">Simprak Asisten</a>
        <ul class="flex items-center space-x-1">
            <?php 
                // Style link aktif dan non-aktif baru
                $activeClass = 'font-semibold';
                $inactiveClass = 'font-medium';
            ?>
            <li><a href="<?php echo $base_path; ?>/asisten/dashboard.php" style="<?php echo ($activePage == 'dashboard') ? 'background-color: #A4CCD9;' : '';?>" class="px-3 py-2 rounded-lg transition-colors text-sm text-gray-800 hover:bg-white/20 <?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?>">Dashboard</a></li>
            <li><a href="<?php echo $base_path; ?>/asisten/kelola_praktikum.php" style="<?php echo ($activePage == 'praktikum') ? 'background-color: #A4CCD9;' : '';?>" class="px-3 py-2 rounded-lg transition-colors text-sm text-gray-800 hover:bg-white/20 <?php echo ($activePage == 'praktikum') ? $activeClass : $inactiveClass; ?>">Kelola Praktikum</a></li>
            <li><a href="<?php echo $base_path; ?>/asisten/kelola_modul.php" style="<?php echo ($activePage == 'modul') ? 'background-color: #A4CCD9;' : '';?>" class="px-3 py-2 rounded-lg transition-colors text-sm text-gray-800 hover:bg-white/20 <?php echo ($activePage == 'modul') ? $activeClass : $inactiveClass; ?>">Kelola Modul</a></li>
            <li><a href="<?php echo $base_path; ?>/asisten/laporan_masuk.php" style="<?php echo ($activePage == 'laporan') ? 'background-color: #A4CCD9;' : '';?>" class="px-3 py-2 rounded-lg transition-colors text-sm text-gray-800 hover:bg-white/20 <?php echo ($activePage == 'laporan') ? $activeClass : $inactiveClass; ?>">Laporan Masuk</a></li>
            <li><a href="<?php echo $base_path; ?>/asisten/kelola_pengguna.php" style="<?php echo ($activePage == 'pengguna') ? 'background-color: #A4CCD9;' : '';?>" class="px-3 py-2 rounded-lg transition-colors text-sm text-gray-800 hover:bg-white/20 <?php echo ($activePage == 'pengguna') ? $activeClass : $inactiveClass; ?>">Kelola Pengguna</a></li>
        </ul>
        <div class="flex items-center space-x-4">
            <span class="font-medium text-gray-700 hidden md:block text-sm">Asisten : <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
            <a href="<?php echo $base_path; ?>/logout.php" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">Logout</a>
        </div>
    </div>
</nav>

<main class="container mx-auto p-6 lg:p-10">
    <h1 class="text-4xl font-extrabold mb-8" style="color: #5C8D97;"><?php echo $pageTitle ?? 'Halaman'; ?></h1>