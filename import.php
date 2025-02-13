<?php
require 'vendor/autoload.php';  // Pastikan path ini sesuai dengan lokasi autoload.php Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

// Koneksi ke MySQL
$servername = "localhost/branding";
$username = "root";
$password = "";
$dbname = "branding"; // Ganti dengan nama database yang sesuai

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek jika file Excel dikirim via form
if (isset($_POST['submit']) && $_FILES['file']['name']) {
    $file = $_FILES['file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    
    // Looping data di Excel
    foreach ($sheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getValue(); // Ambil nilai tiap kolom di row ini
        }

        // Pastikan jumlah data sesuai dengan kolom di tabel MySQL
        $sql = "INSERT INTO nama_tabel (column1, column2, column3) 
                VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $data[2] . "')";
        
        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    echo "Data berhasil diimport!";
} else {
    echo "Silakan pilih file Excel!";
}

$conn->close();
?>

<!-- Form upload file -->
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit" name="submit">Import</button>
</form>
