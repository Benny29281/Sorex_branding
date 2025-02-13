<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Daftar path file Excel sumber
$filePaths = ['branding_JB_JR.xlsx', 'branding_JT_DK_LP.xlsx']; // Array file Excel lo

// Inisialisasi array untuk nyimpen data dari semua file
$allData = [];

// Loop untuk load semua file Excel
foreach ($filePaths as $filePath) {
    // Cek apakah file ada
    if (!file_exists($filePath)) {
        die("File Excel {$filePath} tidak ditemukan!");
    }

    // Load file Excel
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true); // Ambil semua data dari Excel ke array

    // Skip baris pertama (header lama)
    array_shift($data);

    // Tambahkan data dari file ini ke $allData
    $allData = array_merge($allData, $data);
}

// Ambil header dari baris pertama file pertama
$header = isset($allData[0]) ? $allData[0] : [];

// Ambil parameter filter dari URL
$salesName = isset($_GET['salesName']) ? trim($_GET['salesName']) : '';
$startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : '';
$endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : '';

// Filter data berdasarkan input
$filteredData = array_filter($allData, function ($row) use ($salesName, $startDate, $endDate) {
    $salesCol = 'B'; // Asumsi kolom B = Nama Sales
    $dateCol = 'A';  // Asumsi kolom A = Tanggal
    
    $namaSales = isset($row[$salesCol]) ? $row[$salesCol] : '';
    $tanggal = isset($row[$dateCol]) ? $row[$dateCol] : '';

    // Filter Nama Sales (Case Insensitive)
    if ($salesName && stripos($namaSales, $salesName) === false) {
        return false;
    }

    // Filter Tanggal (Format harus sesuai dengan data di Excel)
    if ($startDate && strtotime($tanggal) < strtotime($startDate)) {
        return false;
    }

    if ($endDate && strtotime($tanggal) > strtotime($endDate)) {
        return false;
    }

    return true;
});

// Buat file Excel baru untuk hasil filter
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();

// Set header dari file asli ke file baru
$colIndex = 'A';
foreach ($header as $colValue) {
    $newSheet->setCellValue($colIndex . '1', $colValue);
    $colIndex++;
}

// Isi data yang sudah di-filter
$rowNumber = 2;
foreach ($filteredData as $row) {
    $colIndex = 'A';
    foreach ($row as $cellValue) {
        $newSheet->setCellValue($colIndex . $rowNumber, $cellValue);
        $colIndex++;
    }
    $rowNumber++;
}

// Set header untuk download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="filtered_sales_data.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($newSpreadsheet);
$writer->save('php://output');
exit;
?>
