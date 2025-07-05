<?php
// Logika PHP di bagian atas file tetap sama persis, tidak ada yang diubah.
$pageTitle = 'Kelola Mata Praktikum'; $activePage = 'praktikum';
require_once '../config.php';
require_once 'templates/header.php'; // Navbar profesional sudah dimuat di sini

$message = ''; 
$error = ''; 
$edit_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM mata_praktikum WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Mata praktikum berhasil dihapus."; } 
        else { $error = "Gagal menghapus mata praktikum."; }
        $stmt->close();
    } else {
        $nama_praktikum = trim($_POST['nama_praktikum']);
        $deskripsi = trim($_POST['deskripsi']);
        $id = intval($_POST['id'] ?? 0);
        if (empty($nama_praktikum)) { $error = "Nama praktikum tidak boleh kosong."; } 
        else {
            if ($id > 0) {
                $sql = "UPDATE mata_praktikum SET nama_praktikum = ?, deskripsi = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $nama_praktikum, $deskripsi, $id);
                if ($stmt->execute()) { $message = "Data berhasil diperbarui."; } 
                else { $error = "Gagal memperbarui data."; }
            } else {
                $sql = "INSERT INTO mata_praktikum (nama_praktikum, deskripsi) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $nama_praktikum, $deskripsi);
                if ($stmt->execute()) { $message = "Mata praktikum baru berhasil ditambahkan."; } 
                else { $error = "Gagal menambahkan data."; }
            }
            $stmt->close();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM mata_praktikum WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    $stmt->close();
}
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm font-medium"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm font-medium"><?php echo $error; ?></div>
<?php endif; ?>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $edit_data ? 'Edit Data Praktikum' : 'Tambah Mata Praktikum Baru'; ?></h2>
    <form action="kelola_praktikum.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? 0; ?>">
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-sm font-medium text-gray-700 mb-1">Nama Praktikum</label>
            <input type="text" name="nama_praktikum" id="nama_praktikum" value="<?php echo htmlspecialchars($edit_data['nama_praktikum'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" required>
        </div>
        <div class="mb-6">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"><?php echo htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea>
        </div>
        <div class="flex items-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                <?php echo $edit_data ? 'Update Data' : 'Tambahkan'; ?>
            </button>
            <?php if ($edit_data): ?>
            <a href="kelola_praktikum.php" class="ml-4 text-sm font-medium text-gray-600 hover:text-gray-900">Batal Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Mata Praktikum</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Praktikum</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th scope="col" class="py-3 px-6 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tindakan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $sql = "SELECT * FROM mata_praktikum ORDER BY created_at DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 whitespace-nowrap font-medium text-gray-900"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-4 px-6 text-gray-600"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="kelola_praktikum.php?action=edit&id=<?php echo $row['id']; ?>" class="bg-yellow-100 text-yellow-800 font-semibold py-1 px-3 rounded-full text-xs hover:bg-yellow-200">Edit</a>
                        <form action="kelola_praktikum.php" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus data ini?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="bg-red-100 text-red-800 font-semibold py-1 px-3 rounded-full text-xs hover:bg-red-200">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="3" class="text-center py-10 font-medium text-gray-500">Belum ada data mata praktikum.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>