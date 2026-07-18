<?php
include_once("config.php");

// Tangkap parameter pencarian jika ada (agar PDF sesuai dengan filter)
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

if ($cari != '') {
    $result = mysqli_query($mysqli, "SELECT * FROM alat WHERE nama_alat LIKE '%$cari%' OR merek LIKE '%$cari%' OR lokasi LIKE '%$cari%' ORDER BY id DESC");
} else {
    $result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Alat Elektromedis</title>
    
    <style>
        /* Gaya khusus untuk PDF/Print */
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
        }
        .header-laporan {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-laporan h2 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .header-laporan p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            font-size: 14px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        td {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .footer-laporan {
            margin-top: 30px;
            text-align: right;
            font-size: 14px;
        }

        /* Menyembunyikan elemen yang tidak perlu saat diprint */
        @media print {
            @page { margin: 1.5cm; }
            body { margin: 0; }
        }
    </style>
</head>

<!-- Otomatis memunculkan dialog Print (Simpan sbg PDF) saat halaman dibuka -->
<body onload="window.print()">

    <div class="header-laporan">
        <h2>Laporan Inventaris Alat Elektromedis</h2>
        <p>Rumah Sakit / Klinik</p>
        <?php if($cari != '') { ?>
            <p style="margin-top: 10px;"><em>Filter Pencarian: <strong>"<?php echo $cari; ?>"</strong></em></p>
        <?php } ?>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Nama Alat</th>
                <th width="15%" class="text-center">Tahun</th>
                <th>Merek</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            if(mysqli_num_rows($result) > 0) {
                while($data = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td class='text-center'>".$i."</td>";
                    echo "<td>".$data['nama_alat']."</td>";
                    echo "<td class='text-center'>".$data['tahun']."</td>";
                    echo "<td>".$data['merek']."</td>";
                    echo "<td>".$data['lokasi']."</td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Tidak ada data alat.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="footer-laporan">
        <p>Ngawi, <?php echo date('d F Y'); ?></p>
        <br><br><br>
        <p><strong>Lusyana Dian Afrita Sari</strong></p>
    </div>

</body>
</html>