<?php
// Logika PHP di bagian atas file tetap sama, tidak perlu diubah.
$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';
require_once '../config.php';
require_once 'templates/header.php';

$laporan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

if ($laporan_id == 0) {
    header("Location: laporan_masuk.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nilai = intval($_POST['nilai']);
    $feedback = trim($_POST['feedback']);
    if ($nilai < 0 || $nilai > 100) {
        $error = "Nilai harus di antara 0 dan 100.";
    } else {
        $sql = "UPDATE laporan SET nilai = ?, feedback = ?, status = 'dinilai' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $nilai, $feedback, $laporan_id);
        if ($stmt->execute()) {
            $message = "Nilai berhasil disimpan.";
        } else {
            $error = "Gagal menyimpan nilai.";
        }
        $stmt->close();
    }
}

$sql_detail = "SELECT l.*, u.nama AS nama_mahasiswa, m.judul_modul, mp.nama_praktikum FROM laporan l JOIN users u ON l.mahasiswa_id = u.id JOIN modul m ON l.modul_id = m.id JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id WHERE l.id = ?";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("i", $laporan_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$laporan = $result_detail->fetch_assoc();

if (!$laporan) {
    echo "<p>Laporan tidak ditemukan.</p>";
    require_once 'templates/footer.php';
    exit();
}
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm font-medium"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm font-medium"><?php echo $error; ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Detail Pengumpulan</h2>
        <div class="space-y-4 text-sm">
            <div>
                <p class="font-semibold text-gray-500">Mahasiswa</p>
                <p class="text-lg text-gray-800 mt-1"><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></p>
            </div>
            <div>
                <p class="font-semibold text-gray-500">Praktikum</p>
                <p class="text-lg text-gray-800 mt-1"><?php echo htmlspecialchars($laporan['nama_praktikum']); ?></p>
            </div>
            <div>
                <p class="font-semibold text-gray-500">Modul</p>
                <p class="text-lg text-gray-800 mt-1"><?php echo htmlspecialchars($laporan['judul_modul']); ?></p>
            </div>
            <div>
                <p class="font-semibold text-gray-500">Tanggal Kumpul</p>
                <p class="text-lg text-gray-800 mt-1"><?php echo date('d M Y, H:i', strtotime($laporan['tanggal_kumpul'])); ?></p>
            </div>
        </div>
        <div class="mt-6 border-t border-gray-200 pt-6">
            <a href="../uploads/laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" download class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Unduh Laporan
            </a>
        </div>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Form Penilaian</h2>
        <form action="beri_nilai.php?id=<?php echo $laporan_id; ?>" method="POST" class="space-y-6">
            <div>
                <label for="nilai" class="block text-sm font-medium text-gray-700 mb-1">Nilai (0-100)</label>
                <input type="number" name="nilai" id="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg text-xl font-bold focus:ring-2 focus:ring-blue-500 transition" required>
            </div>
            <div>
                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                <textarea name="feedback" id="feedback" rows="8" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"><?php echo htmlspecialchars($laporan['feedback'] ?? ''); ?></textarea>
            </div>
            <div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors text-lg">Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>

<div class="mt-8">
    <a href="laporan_masuk.php" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Daftar Laporan
    </a>
</div>

<?php
$stmt_detail->close();
require_once 'templates/footer.php';
?>