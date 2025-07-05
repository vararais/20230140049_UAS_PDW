<?php
// Bagian Logika PHP di atas ini tetap sama, tidak perlu diubah.
$pageTitle = 'Kelola Modul'; $activePage = 'modul';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';
$error = '';
$edit_data = null;
$upload_dir = '../uploads/materi/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql_getfile = "SELECT file_materi FROM modul WHERE id = ?";
        $stmt_getfile = $conn->prepare($sql_getfile); $stmt_getfile->bind_param("i", $id); $stmt_getfile->execute(); $result_file = $stmt_getfile->get_result()->fetch_assoc();
        if ($result_file && !empty($result_file['file_materi'])) { if (file_exists($upload_dir . $result_file['file_materi'])) { unlink($upload_dir . $result_file['file_materi']); } }
        $stmt_getfile->close();
        $sql = "DELETE FROM modul WHERE id = ?";
        $stmt = $conn->prepare($sql); $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Modul sudah terhapus."; } else { $error = "Gagal menghapus modul."; }
        $stmt->close();
    } else {
        $mata_praktikum_id = intval($_POST['mata_praktikum_id']); $judul_modul = trim($_POST['judul_modul']); $deskripsi = trim($_POST['deskripsi']); $id = intval($_POST['id'] ?? 0);
        if (empty($judul_modul) || empty($mata_praktikum_id)) { $error = "Wajib isi judul modul dan mata praktikum"; } 
        else {
            $file_materi = $_POST['file_materi_lama'] ?? '';
            if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == UPLOAD_ERR_OK) {
                if ($id > 0 && !empty($file_materi) && file_exists($upload_dir . $file_materi)) { unlink($upload_dir . $file_materi); }
                $file_tmp = $_FILES['file_materi']['tmp_name']; $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
                move_uploaded_file($file_tmp, $upload_dir . $file_name);
                $file_materi = $file_name;
            }
            if ($id > 0) { $sql = "UPDATE modul SET mata_praktikum_id = ?, judul_modul = ?, deskripsi = ?, file_materi = ? WHERE id = ?"; $stmt = $conn->prepare($sql); $stmt->bind_param("isssi", $mata_praktikum_id, $judul_modul, $deskripsi, $file_materi, $id); if ($stmt->execute()) { $message = "Modul sudah terupdate."; } else { $error = "Gagal memperbarui modul."; }
            } else { $sql = "INSERT INTO modul (mata_praktikum_id, judul_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)"; $stmt = $conn->prepare($sql); $stmt->bind_param("isss", $mata_praktikum_id, $judul_modul, $deskripsi, $file_materi); if ($stmt->execute()) { $message = "Modul berhasil terganti."; } else { $error = "Gagal menambahkan modul."; } }
            $stmt->close();
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) { $id = intval($_GET['id']); $sql_edit = "SELECT * FROM modul WHERE id = ?"; $stmt_edit = $conn->prepare($sql_edit); $stmt_edit->bind_param("i", $id); $stmt_edit->execute(); $result_edit = $stmt_edit->get_result(); $edit_data = $result_edit->fetch_assoc(); $stmt_edit->close(); }
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum");
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm font-medium"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm font-medium"><?php echo $error; ?></div>
<?php endif; ?>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $edit_data ? 'Edit Modul' : 'Tambah Modul Baru'; ?></h2>
    <form action="kelola_modul.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? 0; ?>">
        
        <div class="space-y-4">
            <div>
                <label for="mata_praktikum_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Praktikum</label>
                <select name="mata_praktikum_id" id="mata_praktikum_id" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition" required>
                    <option value="">-- Cari dan pilih Mata Praktikum --</option>
                    <?php mysqli_data_seek($praktikum_list, 0); while($prak = $praktikum_list->fetch_assoc()): ?>
                        <option value="<?php echo $prak['id']; ?>" <?php echo (isset($edit_data['mata_praktikum_id']) && $edit_data['mata_praktikum_id'] == $prak['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prak['nama_praktikum']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="judul_modul" class="block text-sm font-medium text-gray-700 mb-1">Tema Modul</label>
                <input type="text" name="judul_modul" id="judul_modul" value="<?php echo htmlspecialchars($edit_data['judul_modul'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" required>
            </div>
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"><?php echo htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea>
            </div>
            <div>
                <label for="file_materi" class="block text-sm font-medium text-gray-700 mb-1">File Materi (ex PDF,DOCX)</label>
                <input type="file" name="file_materi" id="file_materi" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition">
                <?php if (isset($edit_data['file_materi']) && !empty($edit_data['file_materi'])): ?>
                    <p class="text-xs text-gray-500 mt-2">File Sekarang: <a href="<?php echo $upload_dir . htmlspecialchars($edit_data['file_materi']); ?>" class="text-blue-600 font-medium" target="_blank"><?php echo htmlspecialchars($edit_data['file_materi']); ?></a></p>
                    <input type="hidden" name="file_materi_lama" value="<?php echo htmlspecialchars($edit_data['file_materi']); ?>">
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-6 flex items-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors"><?php echo $edit_data ? 'Update Modul' : 'Tambahkan Modul'; ?></button>
            <?php if ($edit_data): ?>
                <a href="kelola_modul.php" class="ml-4 text-sm font-medium text-gray-600 hover:text-gray-900">Batal Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Modul</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tema Modul</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mata Praktikum</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">File Materi</th>
                    <th scope="col" class="py-3 px-6 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tindakan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $sql_read = "SELECT m.id, m.judul_modul, m.file_materi, mp.nama_praktikum FROM modul m JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id ORDER BY mp.nama_praktikum, m.id";
                $result_read = $conn->query($sql_read);
                if ($result_read->num_rows > 0):
                    while($row = $result_read->fetch_assoc()):
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($row['judul_modul']); ?></td>
                    <td class="py-4 px-6 text-gray-600"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-4 px-6 text-gray-600">
                        <?php if(!empty($row['file_materi'])): ?>
                            <a href="<?php echo $upload_dir . htmlspecialchars($row['file_materi']); ?>" target="_blank" class="text-blue-600 hover:underline font-medium">Lihat File</a>
                        <?php else: echo 'Tidak ada'; endif; ?>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="kelola_modul.php?action=edit&id=<?php echo $row['id']; ?>" class="bg-yellow-100 text-yellow-800 font-semibold py-1 px-3 rounded-full text-xs hover:bg-yellow-200">Edit</a>
                        <form action="kelola_modul.php" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus modul ini?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="bg-red-100 text-red-800 font-semibold py-1 px-3 rounded-full text-xs hover:bg-red-200">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="4" class="text-center py-10 font-medium text-gray-500">Belum ada modul yang ditambahkan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>