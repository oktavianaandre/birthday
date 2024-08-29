<?php
include 'db.php';

// Function to generate the next No_Transaksi
function generateNoTransaksi($conn) {
    // Query to get the last No_Transaksi
    $query = "SELECT No_Transaksi FROM TrekamMedis ORDER BY No_Transaksi DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastNo = $row['No_Transaksi'];
        $lastNumber = (int)substr($lastNo, 3); // Extract the number part and convert to integer
        $newNumber = $lastNumber + 1;
        return 'TRX' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } else {
        return 'TRX001'; // Starting code if no records found
    }
}

// Generate new No_Transaksi for the form
$no_transaksi = generateNoTransaksi($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no_transaksi = $_POST['no_transaksi'];
    $kode_peserta = $_POST['kode_peserta'];
    $kode_bidan = $_POST['kode_bidan'];
    $kode_poli = $_POST['kode_poli'];
    $hari = $_POST['tanggal_berobat_hari'];
    $bulan = $_POST['tanggal_berobat_bulan'];
    $tahun = $_POST['tanggal_berobat_tahun'];
    $keluhan = $_POST['keluhan'];
    $biaya_admin = $_POST['biaya_admin'];

    // Gabungkan menjadi format DATE
    $tgl_berobat = "$tahun-$bulan-$hari";

    $sql = "INSERT INTO TrekamMedis (No_Transaksi, Kode_Peserta, Kode_Bidan, Kode_Poli, Tgl_Berobat, Keluhan, Biaya_Admin)
            VALUES ('$no_transaksi', '$kode_peserta', '$kode_bidan', '$kode_poli', '$tgl_berobat', '$keluhan', '$biaya_admin')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to rekam_medis.php after successful insertion
        header("Location: rekam_medis.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Rekam Medis</title>
    <style>
        /* Form Styles */
        form {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            width: 50%;
        }

        form input[type="text"],
        form input[type="number"],
        form select,
        form textarea {
            width: calc(100% - 2rem);
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        form input[type="submit"] {
            background: #333;
            color: #fff;
            border: none;
            padding: 0.75rem;
            border-radius: 4px;
            font-size: 1.2rem;
            cursor: pointer;
            width: 100%;
        }

        form input[type="submit"]:hover {
            background: #555;
        }

        .button-container {
            margin-top: 1rem;
            text-align: center;
        }

        .button-container button {
            background: #007bff;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
<form method="post" action="form_rekam_medis.php">
    No Transaksi: 
    <input type="text" name="no_transaksi" value="<?php echo $no_transaksi; ?>" readonly><br>

    Pasien: 
    <select name="kode_peserta" required>
        <?php
        include 'db.php';
        $result = $conn->query("SELECT * FROM Peserta");
        while ($row = $result->fetch_assoc()) {
            echo "<option value=\"" . $row['Kode_Peserta'] . "\">" . $row['Nama_Peserta'] . "</option>";
        }
        ?>
    </select><br>
    
    Dokter: 
    <select name="kode_bidan" required>
        <?php
        $result = $conn->query("SELECT * FROM Bidan");
        while ($row = $result->fetch_assoc()) {
            echo "<option value=\"" . $row['Kode_Bidan'] . "\">" . $row['Nama_Bidan'] . "</option>";
        }
        ?>
    </select><br>
    
    Poli: 
    <select name="kode_poli" required>
        <?php
        $result = $conn->query("SELECT * FROM Poli");
        while ($row = $result->fetch_assoc()) {
            echo "<option value=\"" . $row['Kode_Poli'] . "\">" . $row['Nama_Poli'] . "</option>";
        }
        ?>
    </select><br>
    
    Tanggal Berobat:
    <select name="tanggal_berobat_hari" required>
        <?php
        for ($i = 1; $i <= 31; $i++) {
            echo "<option value=\"" . str_pad($i, 2, '0', STR_PAD_LEFT) . "\">" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
        }
        ?>
    </select>
    <select name="tanggal_berobat_bulan" required>
        <?php
        $bulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        foreach ($bulan as $key => $value) {
            echo "<option value=\"$key\">$value</option>";
        }
        ?>
    </select>
    <input type="number" name="tanggal_berobat_tahun" placeholder="Tahun" min="1900" max="<?php echo date('Y'); ?>" required><br>

    Keluhan: <textarea name="keluhan" required></textarea><br>
    Biaya Admin: <input type="number" name="biaya_admin" step="0.01" required><br>
    
    <input type="submit" value="Tambah Rekam Medis">

    <div class="button-container">
        <a href="rekam_medis.php">
            <button type="button">Kembali</button>
        </a>
    </div>
    <div class="button-container">
        <a href="dashboard.php">
            <button type="button">Kembali ke Dashboard</button>
        </a>
    </div>
</form>

</body>
</html>
