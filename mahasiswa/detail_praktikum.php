<?php
// Logika PHP di bagian atas file (untuk handle POST, dll) tetap sama
$pageTitle = 'Detail Praktikum'; $activePage = 'praktikum_saya';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$mahasiswa_id = $_SESSION['user_id'];
$praktikum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kumpul_laporan'])) {
    $modul_id = intval($_POST['modul_id']);
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
        $target_dir = "../uploads/laporan/";
        $file_name = time() . '_' . basename($_FILES["file_laporan"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($file_type != "pdf" && $file_type != "doc" && $file_type != "docx") {
            $message = '<div class="mb-4 p-4 rounded-lg font-bold bg-red-100 text-red-800 border border-red-200">Maaf, hanya file PDF, DOC, & DOCX yang diizinkan.</div>';
        } else {
            if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
                $sql_upsert = "INSERT INTO laporan (modul_id, mahasiswa_id, file_laporan, status) VALUES (?, ?, ?, 'dikumpulkan') ON DUPLICATE KEY UPDATE file_laporan = VALUES(file_laporan), tanggal_kumpul = NOW(), status = 'dikumpulkan', nilai = NULL, feedback = NULL";
                $stmt = $conn->prepare($sql_upsert);
                $stmt->bind_param("iis", $modul_id, $mahasiswa_id, $file_name);
                if ($stmt->execute()) {
                    $message = '<div class="mb-4 p-4 rounded-lg font-bold bg-green-100 text-green-800 border border-green-200">Laporan berhasil diunggah. Mantap!</div>';
                } else {
                    $message = '<div class="mb-4 p-4 rounded-lg font-bold bg-red-100 text-red-800 border border-red-200">Gagal menyimpan data laporan.</div>';
                }
                $stmt->close();
            } else { $message = '<div class="mb-4 p-4 rounded-lg font-bold bg-red-100 text-red-800 border border-red-200">Error saat mengunggah file.</div>'; }
        }
    } else { $message = '<div class="mb-4 p-4 rounded-lg font-bold bg-red-100 text-red-800 border border-red-200">Pilih file dulu ya.</div>'; }
}

$sql_praktikum = "SELECT nama_praktikum, deskripsi FROM mata_praktikum WHERE id = ?";
$stmt_praktikum = $conn->prepare($sql_praktikum); $stmt_praktikum->bind_param("i", $praktikum_id); $stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result(); $praktikum = $result_praktikum->fetch_assoc();

if (!$praktikum) { echo "<p>Praktikum tidak ditemukan.</p>"; require_once 'templates/footer_mahasiswa.php'; exit(); }

// Menampilkan pesan (jika ada)
echo $message;
?>

<div class="mb-10">
    <h2 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></h2>
    <p class="text-lg text-gray-600 mt-1"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></p>
</div>

<div class="space-y-6">
    <h3 class="text-2xl font-bold text-gray-700 border-b-2 border-gray-200 pb-2">Daftar Modul</h3>
    <?php
    $sql_modul = "SELECT m.id, m.judul_modul, m.deskripsi, m.file_materi, l.file_laporan, l.tanggal_kumpul, l.status, l.nilai, l.feedback FROM modul m LEFT JOIN laporan l ON m.id = l.modul_id AND l.mahasiswa_id = ? WHERE m.mata_praktikum_id = ? ORDER BY m.id ASC";
    $stmt_modul = $conn->prepare($sql_modul);
    $stmt_modul->bind_param("ii", $mahasiswa_id, $praktikum_id);
    $stmt_modul->execute();
    $result_modul = $stmt_modul->get_result();
    
    if ($result_modul->num_rows > 0):
        while($modul = $result_modul->fetch_assoc()):
    ?>
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($modul['judul_modul']); ?></h4>
                <p class="text-gray-500 text-sm mt-1"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
            </div>
            <?php if (!empty($modul['file_materi'])): ?>
                <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" download class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap text-sm">Unduh Materi</a>
            <?php endif; ?>
        </div>

        <div class="mt-5 border-t border-gray-200 pt-5">
            <h5 class="font-bold text-gray-700 mb-3">Pengumpulan Laporan</h5>
            
            <?php if ($modul['status'] == 'dinilai'): ?>
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">
                    <p class="font-semibold text-green-800">Laporan Terkumpul: <span class="font-normal"><?php echo date('d F Y, H:i', strtotime($modul['tanggal_kumpul'])); ?></span></p>
                    <div class="mt-3 border-t border-green-200 pt-3">
                        <p class="text-sm font-bold">Nilai Anda:</p>
                        <p class="text-3xl font-extrabold text-blue-600"><?php echo htmlspecialchars($modul['nilai']); ?></p>
                        <p class="text-sm font-bold mt-3">Feedback Asisten:</p>
                        <p class="text-gray-700 whitespace-pre-wrap mt-1"><?php echo !empty($modul['feedback']) ? htmlspecialchars($modul['feedback']) : '<i>Tidak ada feedback.</i>'; ?></p>
                    </div>
                </div>
            <?php elseif ($modul['file_laporan']): ?>
                 <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4">
                    <p class="font-semibold text-yellow-800">Laporan Terkumpul: <span class="font-normal"><?php echo date('d F Y, H:i', strtotime($modul['tanggal_kumpul'])); ?></span></p>
                    <p class="text-yellow-800 mt-2 font-semibold">Status: Menunggu Penilaian</p>
                </div>
            <?php endif; ?>

            <?php if ($modul['status'] != 'dinilai'): ?>
            <form action="detail_praktikum.php?id=<?php echo $praktikum_id; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="modul_id" value="<?php echo $modul['id']; ?>">
                <label for="file_laporan_<?php echo $modul['id']; ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $modul['file_laporan'] ? 'Unggah Ulang Laporan (PDF/DOCX)' : 'Unggah Laporan (PDF/DOCX)'; ?></label>
                <div class="flex items-center space-x-3">
                    <input type="file" name="file_laporan" id="file_laporan_<?php echo $modul['id']; ?>" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" required>
                    <button type="submit" name="kumpul_laporan" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-5 rounded-lg text-sm whitespace-nowrap">Kumpul</button>
                </div>
            </form>
            <?php else: ?>
            <div class="p-4 bg-gray-100 rounded-lg text-center">
                <p class="text-sm font-medium text-gray-600">Penilaian telah selesai. Pengumpulan laporan untuk modul ini telah ditutup.</p>
            </div>
            <?php endif; ?>
            </div>
    </div>
    <?php
        endwhile;
    else:
    ?>
    <div class="text-center py-16 px-6 bg-white rounded-xl border border-gray-200">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Modul</h3>
        <p class="mt-1 text-sm text-gray-500">Asisten belum menambahkan modul untuk praktikum ini.</p>
    </div>
    <?php
    endif;
    $stmt_modul->close();
    $conn->close();
    ?>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>