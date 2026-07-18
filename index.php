<?php
include_once("config.php");

// =========================================================================
// KODE BARU: Menangani Fitur Pencarian
// =========================================================================
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

if ($cari != '') {
    // Jika ada pencarian, ambil data berdasarkan nama, merek, atau lokasi
    $result = mysqli_query($mysqli, "SELECT * FROM alat WHERE nama_alat LIKE '%$cari%' OR merek LIKE '%$cari%' OR lokasi LIKE '%$cari%' ORDER BY id DESC");
} else {
    // Jika tidak ada pencarian, tampilkan semua data
    $result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
}

// Menghitung total seluruh alat elektromedis (Tetap global)
$query_total = mysqli_query($mysqli, "SELECT * FROM alat");
$total_alat = mysqli_num_rows($query_total);

// Menghitung total alat yang berada di Poli (Tetap global)
$result_poli = mysqli_query($mysqli, "SELECT * FROM alat WHERE lokasi LIKE '%poli%'");
$total_poli = mysqli_num_rows($result_poli);

// Mengambil data untuk Grafik
$query_grafik = mysqli_query($mysqli, "SELECT lokasi, COUNT(*) as jumlah FROM alat GROUP BY lokasi");
$lokasi_labels = [];
$jumlah_data = [];
while ($row = mysqli_fetch_assoc($query_grafik)) {
    $lokasi_labels[] = $row['lokasi'];
    $jumlah_data[] = $row['jumlah'];
}
?>

<html>
<head>    
    <title>SIM RS - Data Alat Elektromedis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style type="text/css">
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; color: #1e293b; padding: 40px; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* HEADER */
        .dashboard-header { background: linear-gradient(135deg, #2c3e50, #34495e); padding: 25px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; position: relative; overflow: hidden; }
        .header-left { display: flex; align-items: center; gap: 18px; z-index: 2; }
        .profile-photo { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 4px 12px rgba(0,0,0,.3); transition: 0.3s; }
        .profile-photo:hover { transform: scale(1.08); }
        .header-text { color: white; }
        .nama-user { font-size: 13px; color: #cbd5e1; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; }
        .title { font-size: 24px; font-weight: 700; margin: 0; color: #ffffff; }
        .header-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; z-index: 2; }
        .datetime { color: white; text-align: right; font-size: 14px; line-height: 1.5; }
        #tanggal { font-weight: 600; }
        #jam { font-size: 15px; color: #dbeafe; }

        .btn-tambah { background-color: #10b981; color: white; padding: 12px 22px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); transition: all 0.2s ease; border: none; cursor: pointer; }
        .btn-tambah:hover { background-color: #059669; transform: translateY(-2px); }

        /* STATISTIK */
        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-card { background-color: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; justify-content: space-between; }
        .stat-info .stat-label { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
        .stat-info .stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin: 0; }
        .stat-icon { font-size: 32px; padding: 12px; border-radius: 10px; }
        .icon-blue { background-color: #e0f2fe; color: #0284c7; }
        .icon-green { background-color: #d1fae5; color: #059669; }
        .icon-orange { background-color: #ffedd5; color: #ea580c; }

        /* GRAFIK */
        .chart-container { background-color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 25px; height: 350px; display: flex; flex-direction: column; }
        .chart-title { font-size: 16px; font-weight: 700; color: #334155; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .chart-wrapper { position: relative; flex-grow: 1; width: 100%; }

        /* ACTION BAR (PENCARIAN & EXPORT) */
        .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background-color: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .search-form { display: flex; gap: 10px; width: 60%; }
        .search-input { width: 100%; padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: 'Inter', sans-serif; font-size: 14px; outline: none; transition: 0.2s; }
        .search-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
        .btn-search { background-color: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-search:hover { background-color: #2563eb; }
        .btn-reset { background-color: #f1f5f9; color: #64748b; padding: 10px 15px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: 0.2s; display: flex; align-items: center; gap: 5px; font-size: 14px; }
        .btn-reset:hover { background-color: #e2e8f0; color: #334155; }
        .btn-export { background-color: #ef4444; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: 0.2s; font-size: 14px; }
        .btn-export:hover { background-color: #dc2626; transform: translateY(-2px); }

        /* TABEL */
        .table-responsive { background-color: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        table { width: 100%; border-collapse: collapse; border: none; }
        .header { background: linear-gradient(90deg, #f39c12, #e67e22); }
        th { color: white; text-transform: uppercase; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; padding: 18px 20px; text-align: left; }
        td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; font-weight: 500; }
        tr:nth-child(even) { background-color: #f8fafc; }
        tr:hover { background-color: #f1f5f9; transition: background 0.2s ease; }
        .badge-lokasi { background-color: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; display: inline-block; text-transform: uppercase; }
        td:first-child, th:first-child, td:last-child, th:last-child { text-align: center; }

        .btn-action { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; margin: 0 4px; transition: all 0.2s; }
        .btn-edit { background-color: #e0f2fe; color: #0284c7; }
        .btn-edit:hover { background-color: #0284c7; color: white; }
        .btn-delete { background-color: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background-color: #dc2626; color: white; }
    </style>
</head>

<body>
    <div class="container">
        <!-- HEADER DASHBOARD -->
        <div class="dashboard-header">
            <div class="header-left">
                <img src="image/LogoUMPKU.png" alt="Logo UMPKU" class="profile-photo">
                <div class="header-text">
                    <div class="nama-user">LUSYANA DIAN AFRITA SARI</div>
                    <h1 class="title"><i class="fa-solid fa-heart-pulse"></i> Data Alat Elektromedis</h1>
                </div>
            </div>
            <div class="header-right">
                <div class="datetime">
                    <div id="tanggal"></div>
                    <div id="jam"></div>
                </div> 
                <a href="add.php" class="btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Alat</a>
            </div>
        </div>

        <!-- STATISTIK KOTAK -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total Inventaris Alat</div>
                    <h2 class="stat-value"><?php echo $total_alat; ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span></h2>
                </div>
                <div class="stat-icon icon-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total di Area Poli</div>
                    <h2 class="stat-value"><?php echo $total_poli; ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span></h2>
                </div>
                <div class="stat-icon icon-orange"><i class="fa-solid fa-stethoscope"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Status Database</div>
                    <h2 class="stat-value" style="color: #059669; font-size: 22px;">Terhubung</h2>
                </div>
                <div class="stat-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>

        <!-- GRAFIK -->
        <div class="chart-container">
            <div class="chart-title"><i class="fa-solid fa-chart-column" style="color: #0284c7;"></i> Statistik Distribusi Alat per Lokasi</div>
            <div class="chart-wrapper">
                <canvas id="grafikLokasi"></canvas>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- KODE BARU: ACTION BAR (PENCARIAN & EXPORT PDF) -->
        <!-- ========================================================= -->
        <div class="action-bar">
            <!-- Form Pencarian -->
            <form method="GET" action="index.php" class="search-form">
                <input type="text" name="cari" class="search-input" placeholder="Cari nama alat, merek, atau lokasi..." value="<?php echo htmlspecialchars($cari); ?>">
                <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                
                <?php if($cari != '') { ?>
                    <!-- Tombol Reset Muncul jika sedang mencari -->
                    <a href="index.php" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
                <?php } ?>
            </form>

            <!-- Tombol Export PDF -->
            <a href="export_pdf.php?cari=<?php echo urlencode($cari); ?>" target="_blank" class="btn-export">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>

        <!-- TABEL DATA -->
        <div class="table-responsive">
            <table>
                <tr class="header">
                    <th width="8%">No</th>
                    <th>Nama Alat</th>
                    <th>Tahun</th>
                    <th>Merek</th>
                    <th>Lokasi</th>
                    <th width="22%">Aksi</th>
                </tr>
                <?php  
                $i = 1;
                if(mysqli_num_rows($result) > 0) {
                    while($user_data = mysqli_fetch_array($result)) {        
                        echo "<tr>";
                        echo "<td><span style='color: #94a3b8; font-weight: bold;'>#".$i."</span></td>";
                        echo "<td><strong>".$user_data['nama_alat']."</strong></td>";
                        echo "<td>".$user_data['tahun']."</td>";
                        echo "<td>".$user_data['merek']."</td>";    
                        echo "<td><span class='badge-lokasi'>".$user_data['lokasi']."</span></td>";    
                        echo "<td>
                                <a href='edit.php?id=$user_data[id]' class='btn-action btn-edit'><i class='fa-regular fa-pen-to-square'></i> Edit</a>
                                <a href='delete.php?id=$user_data[id]' class='btn-action btn-delete'><i class='fa-regular fa-trash-can'></i> Delete</a>
                              </td>";
                        echo "</tr>"; 
                        $i++;       
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:30px; color:#64748b;'>Data alat tidak ditemukan.</td></tr>";
                }
                ?>
            </table>
        </div>
        
        <p style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 25px; font-weight: 500;">
            Aplikasi dikembangkan oleh: <strong>Lusyana Dian Afrita Sari</strong>
        </p>
    </div>

<script>
// Jam Realtime
function updateDateTime(){
    const sekarang = new Date();
    const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    let tanggal = hari[sekarang.getDay()] + ", " + sekarang.getDate() + " " + bulan[sekarang.getMonth()] + " " + sekarang.getFullYear();
    let jam = sekarang.toLocaleTimeString('id-ID') + " WIB";

    document.getElementById("tanggal").innerHTML = tanggal;
    document.getElementById("jam").innerHTML = jam;
}
updateDateTime();
setInterval(updateDateTime,1000);

// Grafik Chart JS
const ctx = document.getElementById('grafikLokasi').getContext('2d');
const labels_lokasi = <?php echo json_encode($lokasi_labels); ?>;
const data_jumlah = <?php echo json_encode($jumlah_data); ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels_lokasi,
        datasets: [{
            label: 'Jumlah Alat',
            data: data_jumlah,
            backgroundColor: ['rgba(14, 165, 233, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(239, 68, 68, 0.7)'],
            borderColor: ['rgb(14, 165, 233)', 'rgb(16, 185, 129)', 'rgb(245, 158, 11)', 'rgb(139, 92, 246)', 'rgb(239, 68, 68)'],
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
</body>
</html>