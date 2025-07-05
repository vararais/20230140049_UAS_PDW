<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
$mahasiswa_id = $_SESSION['user_id'];
$stmt_praktikum = $conn->prepare("SELECT COUNT(*) AS total FROM pendaftaran_praktikum WHERE mahasiswa_id = ?");
$stmt_praktikum->bind_param("i", $mahasiswa_id);
$stmt_praktikum->execute();
$praktikum_diikuti = $stmt_praktikum->get_result()->fetch_assoc()['total'] ?? 0;
$stmt_praktikum->close();

$stmt_selesai = $conn->prepare("SELECT COUNT(*) AS total FROM laporan WHERE mahasiswa_id = ? AND status = 'dinilai'");
$stmt_selesai->bind_param("i", $mahasiswa_id);
$stmt_selesai->execute();
$tugas_selesai = $stmt_selesai->get_result()->fetch_assoc()['total'] ?? 0;
$stmt_selesai->close();

$stmt_menunggu = $conn->prepare("SELECT COUNT(*) AS total FROM laporan WHERE mahasiswa_id = ? AND status = 'dikumpulkan'");
$stmt_menunggu->bind_param("i", $mahasiswa_id);
$stmt_menunggu->execute();
$tugas_menunggu = $stmt_menunggu->get_result()->fetch_assoc()['total'] ?? 0;
$stmt_menunggu->close();

$stmt_notif = $conn->prepare("SELECT m.judul_modul, mp.nama_praktikum FROM laporan l JOIN modul m ON l.modul_id = m.id JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id WHERE l.mahasiswa_id = ? AND l.status = 'dinilai' ORDER BY l.tanggal_kumpul DESC LIMIT 3");
$stmt_notif->bind_param("i", $mahasiswa_id);
$stmt_notif->execute();
$notifikasi_terbaru = $stmt_notif->get_result();
?>

<div class="mb-10">
    <h1 class="text-3xl font-bold" style="color: #555879;">Hai, <?php echo htmlspecialchars($_SESSION['nama']); ?>! </h1>
    <p class="mt-2 text-lg text-gray-600">Senang kamu bergabung. Jangan lupa pahami dan kerjakan praktikum</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white/70 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm h-full">
            <h3 class="text-xl font-bold" style="color: #555879;">Pemberitahuan</h3>
            <div class="space-y-4 mt-4">
                 <?php if ($notifikasi_terbaru->num_rows > 0): while($notif = $notifikasi_terbaru->fetch_assoc()): ?>
                    <div class="flex items-start p-4 rounded-lg" style="background-color: #DED3C4;">
                         <div class="ml-3 w-0 flex-1">
                            <p class="text-sm" style="color: #555879;">
                                Saat ini Nilai kamu, <?php echo htmlspecialchars($notif['judul_modul']); ?></span> (Praktikum <?php echo htmlspecialchars($notif['nama_praktikum']); ?>) udah selesai dinilai
                                <a href="praktikum_saya.php" class="ml-2 hover:underline" style="color: #555879;">Lihat Hasilnya!</a>
                            </p>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                    <p class="text-sm text-gray-600">Tidak ada notifikasi baru untuk Anda saat ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white/70 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-6">
            <div class="p-3 rounded-full" style="background-color: #DED3C4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #555879;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Praktikum Diikuti</p>
                <p class="text-3xl font-bold mt-1" style="color: #555879;"><?php echo $praktikum_diikuti; ?></p>
            </div>
        </div>
        
        <div class="bg-white/70 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-6">
            <div class="p-3 rounded-full" style="background-color: #DED3C4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #555879;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Tugas Selesai Dinilai</p>
                <p class="text-3xl font-bold mt-1" style="color: #555879;"><?php echo $tugas_selesai; ?></p>
            </div>
        </div>
        
        <div class="bg-white/70 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-6">
            <div class="p-3 rounded-full" style="background-color: #DED3C4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #555879;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Menunggu Penilaian</p>
                <p class="text-3xl font-bold mt-1" style="color: #555879;"><?php echo $tugas_menunggu; ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>