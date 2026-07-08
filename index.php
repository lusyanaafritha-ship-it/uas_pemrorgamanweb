<?php
include_once("config.php");

// Mengambil data dari database
$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
?>

<html>
<head>    
    <title>Sim Rs</title>
    <style type="text/css">
        /* Mengubah font default agar lebih modern & bersih */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding: 40px;
            margin: 0;
        }

        /* Styling judul */
        .title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
            display: block;
        }

        /* Mengubah link "Tambah Alat" menjadi tombol hijau yang estetik */
        .btn-tambah {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(46, 204, 113, 0.3);
            transition: all 0.2s ease;
        }
        .btn-tambah:hover {
            background-color: #27ae60;
            transform: translateY(-1px);
        }

        /* Styling Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: none;
        }

        /* Styling Kepala Tabel (Header) */
        .header {
            background-color: #f39c12; /* Warna orange khas kamu, tapi dibuat lebih rata */
        }
        th {
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        /* Styling Isi Tabel (Baris & Kolom) */
        td {
            padding: 14px 15px;
            border-bottom: 1px solid #edf2f7;
            font-size: 15px;
            color: #4a5568;
        }

        /* Efek Zebra (Belang-belang abu-abu tipis) */
        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Efek Hover saat baris disorot mouse */
        tr:hover {
            background-color: #f1f5f9;
            transition: background 0.2s;
        }

        /* Merapikan kolom nomor dan aksi ke tengah */
        td:first-child, th:first-child,
        td:last-child, th:last-child {
            text-align: center;
        }

        /* Mengubah link Edit & Delete menjadi tombol mini */
        .btn-action {
            display: inline-block;
            padding: 6px 14px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            margin: 0 4px;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background-color: #3498db;
            color: white;
        }
        .btn-edit:hover {
            background-color: #2980b9;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
    </style>
</head>

<body>
    <!-- Tambahkan baris kode nama di bawah ini -->
    <div class="nama-user">Lusyana Dian Afrita Sari</div>
    
    <b class="title">Data Alat Elektromedis</b>
    <a href="add.php" class="btn-tambah">+ Tambah Alat</a>

    <table>
        <tr class="header">
            <th width="5%">No</th>
            <th>Nama Alat</th>
            <th>Tahun</th>
            <th>Merek</th>
            <th>Lokasi</th>
            <th width="20%">Aksi</th>
        </tr>
        <?php  
        $i = 1;
        while($user_data = mysqli_fetch_array($result)) {         
            echo "<tr>";
            echo "<td>".$i."</td>";
            echo "<td>".$user_data['nama_alat']."</td>";
            echo "<td>".$user_data['tahun']."</td>";
            echo "<td>".$user_data['merek']."</td>";    
            echo "<td>".$user_data['lokasi']."</td>";    
            echo "<td>
                    <a href='edit.php?id=$user_data[id]' class='btn-action btn-edit'>Edit</a>
                    <a href='delete.php?id=$user_data[id]' class='btn-action btn-delete'>Delete</a>
                  </td>";
            echo "</tr>"; 
            $i++;       
        }
        ?>
    </table>
</body>
</html>