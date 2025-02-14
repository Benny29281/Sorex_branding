<?php
// Include autoloader PhpSpreadsheet
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = trim($_POST['search']); // Ambil input pencarian
    $search = strtolower($search);   // Ubah ke huruf kecil agar pencarian tidak case-sensitive

    // List file Excel yang mau dibaca
    $files = ['branding_JB_JR.xlsx','branding_JT_DK_LP.xlsx']; // Tambahkan file Excel di sini
    $results = [];

    foreach ($files as $file) {
        if (file_exists($file)) {
            // Baca file Excel
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Loop melalui setiap baris
            foreach ($sheet->getRowIterator() as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Include semua sel, meskipun kosong
                
                // Ambil data dari setiap sel di baris
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getFormattedValue();
                }
                
                // Cek apakah input pencarian cocok dengan salah satu kolom (seluruh baris)
                $rowText = strtolower(implode(' ', $rowData)); // Gabungkan seluruh data per baris untuk pencarian
                if (strpos($rowText, $search) !== false) {
                    $results[] = $rowData; // Jika cocok, simpan hasilnya
                }
            }
        }
    }

    if (!empty($results)) {
        // Generate file Excel untuk download
        $outputSpreadsheet = new Spreadsheet();
        $outputSheet = $outputSpreadsheet->getActiveSheet();

        // Tambahkan header ke file Excel
        $headers = ["TANGGAL", "NAMA SALES", "REQUEST CODE", "NAMA TOKO", "AREA", "TIPE (B/P)", "VIA", "PERMINTAAN", "QTY", "PEMBUATAN DESIGN PIC: TEAM DESIGN",
                    "APPROVE LADDER", "APPROVAL TOKO PIC:TEAM SALES", "KONFIRMASI DESIGN PIC: TEAM SALES", "KIRIM PO PIC:TEAM DESIGN TANGGAL", "VENDOR", "SJ DI TERIMA TASYA",
                    "PO SELESAI DI BUAT DAN DI KIRIM KE K", "GUDANG K PACKING", "KIRIM", "DADAP TERIMA DARI GUDANG K", "KIRIM", "KONFIRMASI PENERIMAAN PIC:TEAM SALES"];
        $outputSheet->fromArray($headers, null, 'A1');

        // Tambahkan data hasil pencarian ke file Excel
        $outputSheet->fromArray($results, null, 'A2');

        // Simpan file Excel sementara
        $filename = 'hasil_pencarian.xlsx';
        $writer = new Xlsx($outputSpreadsheet);
        $writer->save($filename);

        // Tampilkan tombol download di atas tabel
        echo "<div style='display: flex; justify-content: space-between; align-items: center;'>";
        echo "<h1>Hasil Pencarian:</h1>";
        echo "<a href='$filename' download style='margin-right: 20px; text-decoration: none; background:rgb(250, 39, 23); color: white; padding: 10px 15px; border-radius: 5px;'>Download Hasil Pencarian</a>";
        echo "</div>";

        // Tampilkan tabel hasil pencarian
        echo "<table border='1'>";
        echo "<tr><th>TANGGAL</th><th>NAMA SALES</th><th>REQUEST CODE</th><th>NAMA TOKO</th><th>AREA</th><th>TIPE (B/P)</th><th>VIA</th><th>PERMINTAAN</th><th>QTY</th><th>PEMBUATAN DESIGN PIC: TEAM DESIGN</th>
        <th>APPROVE LADDER</th><th>APPROVAL TOKO PIC:TEAM SALES</th><th>KONFIRMASI DESIGN PIC: TEAM SALES</th><th>KIRIM PO PIC:TEAM DESIGN TANGGAL</th><th>VENDOR</th><th>SJ DI TERIMA TASYA</th>
        <th>PO SELESAI DI BUAT DAN DI KIRIM KE K</th><th>GUDANG K PACKING</th><th>KIRIM</th><th>DADAP TERIMA DARI GUDANG K</th><th>KIRIM</th><th>KONFIRMASI PENERIMAAN PIC:TEAM SALES</th></tr>";
        foreach ($results as $row) {
            echo "<tr><td>" . implode('</td><td>', $row) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<h1>Tidak ada data yang cocok!</h1>";
    }
}
?>