<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php';
require_once 'templates/header.php';

// Logika PHP untuk mengambil data tetap sama
$result_modul = $conn->query("SELECT COUNT(*) AS total FROM modul");
$total_modul = $result_modul->fetch_assoc()['total'] ?? 0;
$result_laporan = $conn->query("SELECT COUNT(*) AS total FROM laporan");
$total_laporan = $result_laporan->fetch_assoc()['total'] ?? 0;
$result_belum_dinilai = $conn->query("SELECT COUNT(*) AS total FROM laporan WHERE status = 'dikumpulkan'");
$laporan_belum_dinilai = $result_belum_dinilai->fetch_assoc()['total'] ?? 0;
$aktivitas_terbaru = $conn->query("SELECT u.nama AS nama_mahasiswa, m.judul_modul, l.tanggal_kumpul FROM laporan l JOIN users u ON l.mahasiswa_id = u.id JOIN modul m ON l.modul_id = m.id ORDER BY l.tanggal_kumpul DESC LIMIT 5");
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm h-full">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Laporan Aktivitas Mu</h3>
            <div class="space-y-4">
                <?php if ($aktivitas_terbaru->num_rows > 0): while ($aktivitas = $aktivitas_terbaru->fetch_assoc()): ?>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center" style="background-color: #C4E1E6;">
                            <span class="text-sm font-bold" style="color: #5C8D97;"><?php echo strtoupper(substr($aktivitas['nama_mahasiswa'], 0, 2)); ?></span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-800">
                                <?php echo htmlspecialchars($aktivitas['nama_mahasiswa']); ?></span>
                                <span class="text-gray-600">telah mengumpulkan lampiran buat</span>
                                <?php echo htmlspecialchars($aktivitas['judul_modul']); ?></span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1"><?php echo date('d F Y, H:i', strtotime($aktivitas['tanggal_kumpul'])); ?></p>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                    <div class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Semua Lampiran udah dinilai</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-6">
            <div class="p-3 rounded-full" style="background-color: #C4E1E6;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #5C8D97;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Semua Modul</p>
                <p class="text-3xl font-bold mt-1" style="color: #5C8D97;"><?php echo $total_modul; ?></p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-6">
            <div class="p-3 rounded-full" style="background-color: #C4E1E6;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #5C8D97;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h6.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Lampiran Masuk</p>
                <p class="text-3xl font-bold mt-1" style="color: #5C8D97;"><?php echo $total_laporan; ?></p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-6">
            <div class="p-3 rounded-full" style="background-color: #C4E1E6;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #5C8D97;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Belum Ternilai</p>
                <p class="text-3xl font-bold mt-1" style="color: #5C8D97;"><?php echo $laporan_belum_dinilai; ?></p>
            </div>
        </div>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>