<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna adalah mahasiswa yang sudah login
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'mahasiswa') {

    // ==========================================================
    // TAMPILAN JIKA MAHASISWA SUDAH LOGIN (Tidak Berubah)
    // ==========================================================
    $pageTitle = 'Katalog Praktikum';
    $activePage = 'katalog';
    require_once 'mahasiswa/templates/header_mahasiswa.php';

    $pendaftaran_ids = [];
    $mahasiswa_id = $_SESSION['user_id'];
    $sql_pendaftaran = "SELECT mata_praktikum_id FROM pendaftaran_praktikum WHERE mahasiswa_id = ?";
    $stmt_pendaftaran = $conn->prepare($sql_pendaftaran);
    $stmt_pendaftaran->bind_param("i", $mahasiswa_id);
    $stmt_pendaftaran->execute();
    $result_pendaftaran = $stmt_pendaftaran->get_result();
    while ($row_pendaftaran = $result_pendaftaran->fetch_assoc()) {
        $pendaftaran_ids[] = $row_pendaftaran['mata_praktikum_id'];
    }
    $stmt_pendaftaran->close();
    
    $sql = "SELECT * FROM mata_praktikum ORDER BY nama_praktikum ASC";
    $result = $conn->query($sql);
?>
    <?php if(isset($_GET['status'])): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $_GET['status'] == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo htmlspecialchars($_GET['pesan']); ?>
        </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col p-6">
                <h3 class="font-bold text-xl text-gray-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                <p class="text-gray-600 mt-2 flex-grow"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                <div class="mt-6">
                    <?php if (in_array($row['id'], $pendaftaran_ids)): ?>
                        <div class="flex items-center justify-center w-full bg-green-100 text-green-800 font-semibold py-2 px-4 rounded-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Sudah Terdaftar</span>
                        </div>
                    <?php else: ?>
                        <a href="mahasiswa/daftar_praktikum.php?id=<?php echo $row['id']; ?>" class="block w-full text-center bg-gray-800 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-900 transition-colors">
                            Daftar Praktikum
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; else: ?>
            <p class="col-span-full text-center text-slate-500 font-bold text-xl py-10">Waduh, belum ada praktikum yang tersedia saat ini. 😢</p>
        <?php endif; ?>
    </div>

<?php
    require_once 'mahasiswa/templates/footer_mahasiswa.php';

} else {

    // ==========================================================
    // TAMPILAN JIKA PENGGUNA ADALAH TAMU (YANG DIPERBARUI)
    // ==========================================================
?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Selamat Datang di SIMPRAK</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <style>body { font-family: 'Inter', sans-serif; }</style>
    </head>
    <body class="bg-gray-50 flex flex-col min-h-screen">
        <nav class="bg-white shadow-sm">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <a href="katalog.php" class="text-2xl font-bold text-indigo-600">SIMPRAK</a>
                <div class="space-x-4">
                    <a href="login.php" class="font-medium text-gray-600 hover:text-indigo-600">Login</a>
                    <a href="register.php" class="bg-indigo-600 text-white font-semibold py-2 px-5 rounded-lg hover:bg-indigo-700 transition-colors">Daftar</a>
                </div>
            </div>
        </nav>

        <main class="flex-grow flex flex-col items-center justify-center text-center p-6">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800">Selamat datang di Website SIMPRAK 😆</h1>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Saat ini belum ada Praktikum yaaa, silahkan Login atau Daftar terlebih dahulu 😁.
                </p>
            </div>
        </main>
        
        </body>
    </html>
<?php
}

if (isset($conn)) {
    $conn->close();
}
?>