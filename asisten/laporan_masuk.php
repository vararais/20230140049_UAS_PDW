<?php
// Logika PHP di bagian atas file tetap sama, tidak perlu diubah.
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once '../config.php';
require_once 'templates/header.php';

$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum");
$mahasiswa_list = $conn->query("SELECT id, nama FROM users WHERE role = 'mahasiswa' ORDER BY nama");
$where_clauses = [];
$params = [];
$types = '';

if (!empty($_GET['praktikum_id'])) { $where_clauses[] = 'mp.id = ?'; $params[] = $_GET['praktikum_id']; $types .= 'i'; }
if (!empty($_GET['mahasiswa_id'])) { $where_clauses[] = 'u.id = ?'; $params[] = $_GET['mahasiswa_id']; $types .= 'i'; }
if (!empty($_GET['status'])) { $where_clauses[] = 'l.status = ?'; $params[] = $_GET['status']; $types .= 's'; }

$sql_read = "SELECT l.id, u.nama AS nama_mahasiswa, mp.nama_praktikum, m.judul_modul, l.tanggal_kumpul, l.status FROM laporan l JOIN users u ON l.mahasiswa_id = u.id JOIN modul m ON l.modul_id = m.id JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id";
if (!empty($where_clauses)) { $sql_read .= " WHERE " . implode(' AND ', $where_clauses); }
$sql_read .= " ORDER BY l.tanggal_kumpul DESC";

$stmt = $conn->prepare($sql_read);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result_read = $stmt->get_result();
?>

<div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Seleksi Laporan</h2>
    <form action="laporan_masuk.php" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="praktikum_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Praktikum</label>
                <select name="praktikum_id" id="praktikum_id" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Semua</option>
                    <?php mysqli_data_seek($praktikum_list, 0); while($prak = $praktikum_list->fetch_assoc()): ?>
                        <option value="<?php echo $prak['id']; ?>" <?php echo (isset($_GET['praktikum_id']) && $_GET['praktikum_id'] == $prak['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($prak['nama_praktikum']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="mahasiswa_id" class="block text-sm font-medium text-gray-700 mb-1">Mahasiswa</label>
                <select name="mahasiswa_id" id="mahasiswa_id" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Semua</option>
                    <?php mysqli_data_seek($mahasiswa_list, 0); while($mhs = $mahasiswa_list->fetch_assoc()): ?>
                        <option value="<?php echo $mhs['id']; ?>" <?php echo (isset($_GET['mahasiswa_id']) && $_GET['mahasiswa_id'] == $mhs['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($mhs['nama']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Semua</option>
                    <option value="dikumpulkan" <?php echo (isset($_GET['status']) && $_GET['status'] == 'dikumpulkan') ? 'selected' : ''; ?>>Belum Dinilai</option>
                    <option value="dinilai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <div class="flex space-x-3">
                <a href="laporan_masuk.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">Refresh</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Saring</button>
            </div>
        </div>
    </form>
</div>


<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-6 md:p-8 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Hasil Laporan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Praktikum</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Modul</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Kumpul</th>
                    <th scope="col" class="py-3 px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="py-3 px-6 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tindakan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result_read->num_rows > 0): while($row = $result_read->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                    <td class="py-4 px-6 text-gray-600"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-4 px-6 text-gray-600"><?php echo htmlspecialchars($row['judul_modul']); ?></td>
                    <td class="py-4 px-6 text-gray-600"><?php echo date('d M Y, H:i', strtotime($row['tanggal_kumpul'])); ?></td>
                    <td class="py-4 px-6">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $row['status'] == 'dinilai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $row['status'] == 'dinilai' ? 'Dinilai' : 'Menunggu'; ?>
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <a href="beri_nilai.php?id=<?php echo $row['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded-md text-xs transition-colors">
                            <?php echo $row['status'] == 'dinilai' ? 'Lihat' : 'Masukkan Nilai'; ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="text-center py-10 font-medium text-gray-500">Tidak ada laporan yang cocok dengan filter.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $stmt->close(); require_once 'templates/footer.php'; ?>