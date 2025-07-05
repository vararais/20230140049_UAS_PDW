<?php
$pageTitle = 'Praktikum Ku';
$activePage = 'praktikum_saya';
require_once 'templates/header_mahasiswa.php'; // Navbar sudah dimuat di sini
require_once '../config.php';
$mahasiswa_id = $_SESSION['user_id'];
?>

<?php if(isset($_GET['status'])): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $_GET['status'] == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
        <?php echo htmlspecialchars($_GET['pesan']); ?>
    </div>
<?php endif; ?>

<div class="space-y-6">
    <?php
    $sql = "SELECT mp.id, mp.nama_praktikum, mp.deskripsi FROM mata_praktikum mp JOIN pendaftaran_praktikum pp ON mp.id = pp.mata_praktikum_id WHERE pp.mahasiswa_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mahasiswa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0):
        while($row = $result->fetch_assoc()):
    ?>
    
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-xl text-gray-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
            <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
        </div>
        <a href="detail_praktikum.php?id=<?php echo $row['id']; ?>" class="bg-gray-800 text-white font-bold py-2 px-5 rounded-lg hover:bg-gray-900 transition-colors whitespace-nowrap ml-6">
            Lihat Detail
        </a>
    </div>

    <?php
        endwhile;
    else:
    ?>

    <div class="text-center py-16 px-6 bg-white rounded-xl border border-gray-200">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Praktikum</h3>
        <p class="mt-1 text-sm text-gray-500">Anda belum terdaftar di praktikum manapun.</p>
        <div class="mt-6">
            <a href="../katalog.php" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold py-2 px-5 rounded-lg">
                Jelajahi Katalog
            </a>
        </div>
    </div>
    
    <?php
    endif;
    $stmt->close();
    $conn->close();
    ?>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>