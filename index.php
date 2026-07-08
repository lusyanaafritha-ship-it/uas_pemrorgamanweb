<?php
include_once("config.php");

// Mengambil data dari database
$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");

// Menghitung total seluruh alat elektromedis
$total_alat = mysqli_num_rows($result);

// Menghitung total alat yang berada di Poli (Contoh statistik dinamis)
$result_poli = mysqli_query($mysqli, "SELECT * FROM alat WHERE lokasi LIKE '%poli%'");
$total_poli = mysqli_num_rows($result_poli);
?>

<html>
<head>    
    <title>SIM RS - Data Alat Elektromedis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style type="text/css">
        /* Menggunakan font Inter standar aplikasi modern */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 40px;
            margin: 0;
        }

        /* Container Pembungkus */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            /* Tambahan animasi smooth fade-in saat halaman dibuka */
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Card Atas untuk Nama dan Judul */
        .dashboard-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .header-text {
            color: white;
            z-index: 2;
        }

        .nama-user {
            font-size: 13px;
            color: #cbd5e1;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
        }

        /* Elemen Gambar Medis Tambahan */
        .header-illustration {
            position: absolute;
            right: 220px;
            top: 50%;
            transform: translateY(-50%);
            height: 100px;
            opacity: 0.85;
            z-index: 1;
            pointer-events: none;
        }
        
        .header-illustration img {
            height: 100%;
            object-fit: contain;
        }

        /* Tombol Tambah Alat Modern */
        .btn-tambah {
            background-color: #10b981;
            color: white;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
            z-index: 2;
        }
        .btn-tambah:hover {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }

        /* STYLING KOTAK STATISTIK (BARU) */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background-color: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info .stat-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-info .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .stat-icon {
            font-size: 32px;
            padding: 12px;
            border-radius: 10px;
        }

        /* Variasi Warna Icon Ringkasan */
        .icon-blue { background-color: #e0f2fe; color: #0284c7; }
        .icon-green { background-color: #d1fae5; color: #059669; }
        .icon-orange { background-color: #ffedd5; color: #ea580c; }

        /* Desain Tabel Premium */
        .table-responsive {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        /* Header Tabel (Orange Elegan) */
        .header {
            background: linear-gradient(90deg, #f39c12, #e67e22);
        }
        th {
            color: white;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            padding: 18px 20px;
            text-align: left;
        }

        /* Isi Baris Tabel */
        td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #334155;
            font-weight: 500;
        }

        /* Efek Zebra Ringan */
        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Efek Hover halus */
        tr:hover {
            background-color: #f1f5f9;
            transition: background 0.2s ease;
        }

        /* Desain Badge Kolom Lokasi */
        .badge-lokasi {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
        }

        /* Penyelarasan Kolom Tengah */
        td:first-child, th:first-child,
        td:last-child, th:last-child {
            text-align: center;
        }

        /* Tombol Aksi Keren */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            margin: 0 4px;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background-color: #e0f2fe;
            color: #0284c7;
        }
        .btn-edit:hover {
            background-color: #0284c7;
            color: white;
        }

        .btn-delete {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .btn-delete:hover {
            background-color: #dc2626;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="header-text">
                <div class="nama-user">Sistem Informasi Manajemen RS</div>
                <h1 class="title"><i class="fa-solid fa-heart-pulse"></i> Data Alat Elektromedis</h1>
            </div>
            
            <div class="header-illustration">
                <img src="https://img.icons8.com/illustrations/colorful/160/medical-doctor.png" alt="Medical Illustration">
            </div>

            <a href="add.php" class="btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Alat</a>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total Inventaris Alat</div>
                    <h2 class="stat-value"><?php echo $total_alat; ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span></h2>
                </div>
                <div class="stat-icon icon-blue">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total di Area Poli</div>
                    <h2 class="stat-value"><?php echo $total_poli; ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span></h2>
                </div>
                <div class="stat-icon icon-orange">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Status Database</div>
                    <h2 class="stat-value" style="color: #059669; font-size: 22px;">Terhubung</h2>
                </div>
                <div class="stat-icon icon-green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

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
                ?>
            </table>
        </div>
        
        <p style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 25px; font-weight: 500;">
            Aplikasi dikembangkan oleh: <strong>Lusyana Dian Afrita Sari</strong>
        </p>
    </div>
</body>
</html>