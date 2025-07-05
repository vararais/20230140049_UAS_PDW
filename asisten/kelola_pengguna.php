<?php
// Bagian Logika PHP di atas ini tetap sama, tidak perlu diubah.
$pageTitle = 'Kelola Pengguna';
$activePage = 'pengguna';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';
$error = '';
$edit_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'delete') {
        $id = intval($_POST['id']);
        if ($id == $_SESSION['user_id']) {
            $error = "Anda tidak dapat menghapus akun Anda sendiri.";
        } else {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Pengguna berhasil dihapus.";
            } else {
                $error = "Gagal menghapus pengguna.";
            }
            $stmt->close();
        }
    } 
    else {
        $id = intval($_POST['id'] ?? 0);
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);
        $password = trim($_POST['password']);
        if (empty($nama) || empty($email) || empty($role)) {
            $error = "Nama, Email, dan Peran tidak boleh kosong.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format email tidak valid.";
        } else {
            if ($id > 0) {
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $nama, $email, $role, $hashed_password, $id);
                } else {
                    $sql = "UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssi", $nama, $email, $role, $id);
                }
                if ($stmt->execute()) { $message = "Data pengguna berhasil diperbarui."; } 
                else { $error = "Gagal memperbarui data pengguna. Mungkin email sudah digunakan."; }
            } 
            else {
                if (empty($password)) { $error = "Password tidak boleh kosong untuk pengguna baru."; } 
                else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
                    if ($stmt->execute()) { $message = "Pengguna baru berhasil ditambahkan."; } 
                    else { $error = "Gagal menambahkan pengguna. Mungkin email sudah digunakan."; }
                }
            }
            $stmt->close();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql_edit = "SELECT id, nama, email, role FROM users WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("i", $id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $edit_data = $result_edit->fetch_assoc();
    $stmt_edit->close();
}
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm font-medium"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm font-medium"><?php echo $error; ?></div>
<?php endif; ?>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $edit_data ? 'Edit Pengguna' : 'Tambah Pengguna Baru'; ?></h2>
    <form action="kelola_pengguna.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? 0; ?>">
        <div class="space-y-4">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($edit_data['nama'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" <?php echo !$edit_data ? 'required' : ''; ?>>
                <?php if ($edit_data): ?>
                <p class="text-xs text-gray-500 mt-2">Kosongkan jika tidak ingin mengubah password.</p>
                <?php endif; ?>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                <select name="role" id="role" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition" required>
                    <option value="mahasiswa" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                    <option value="asisten" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex items-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                <?php echo $edit_data ? 'Update Pengguna' : 'Tambah Pengguna'; ?>
            </button>
            <?php if ($edit_data): ?>
            <a href="kelola_pengguna.php" class="ml-4 text-sm font-medium text-gray-600 hover:text-gray-900">Batal Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">List Semua Pengguna</h2>
    <div class="space-y-4">
        <?php
        $sql_read = "SELECT id, nama, email, role, created_at FROM users ORDER BY created_at DESC";
        $result_read = $conn->query($sql_read);
        if ($result_read->num_rows > 0):
            while($row = $result_read->fetch_assoc()):
        ?>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-200 text-blue-800 flex items-center justify-center font-bold text-sm">
                    <?php echo strtoupper(substr($row['nama'], 0, 2)); ?>
                </div>
                <div>
                    <p class="font-bold text-gray-900"><?php echo htmlspecialchars($row['nama']); ?>
                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full capitalize <?php echo ($row['role'] == 'asisten') ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800'; ?>">
                            <?php echo htmlspecialchars($row['role']); ?>
                        </span>
                    </p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['email']); ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-sm">
                <a href="kelola_pengguna.php?action=edit&id=<?php echo $row['id']; ?>" class="bg-yellow-100 text-yellow-800 font-semibold py-1 px-3 rounded-full text-xs hover:bg-yellow-200">Edit</a>
                <form action="kelola_pengguna.php" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="bg-red-100 text-red-800 font-semibold py-1 px-3 rounded-full text-xs hover:bg-red-200">Hapus</button>
                </form>
            </div>
        </div>
        <?php endwhile; else: ?>
        <p class="text-center py-10 font-medium text-gray-500">Tidak ada pengguna terdaftar.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>