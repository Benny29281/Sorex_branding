<?php
require 'vendor/autoload.php'; // pastikan pathnya sesuai

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil inputan dari form
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $region = $_POST['region'];

    // Path ke file Excel yang ada di dalam project (misalnya 'data.xlsx')
    $fileName = 'data.xlsx';

    if (file_exists($fileName)) {
        // Load file Excel
        $spreadsheet = IOFactory::load($fileName);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Ambil semua data dalam file Excel (termasuk header)
        $rows = $sheet->toArray(); // Mengambil semua baris data sebagai array
        
        // Ambil header dari baris pertama
        $header = array_shift($rows);

        // Filter data berdasarkan tanggal dan region
        $filteredData = array_filter($rows, function($row) use ($date_from, $date_to, $region) {
            // Anggap kolom tanggal ada di index 0 dan region di index 1
            return ($row[0] >= $date_from && $row[0] <= $date_to) && $row[1] == $region;
        });

        // Create Spreadsheet untuk output
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();

        // Set header Excel
        $colIndex = 1; // Mulai dari kolom A (index 1)
        foreach ($header as $colName) {
            // Set header menggunakan setCellValue
            $newSheet->setCellValue($colIndex, 4, $colName);
            $colIndex++;
        }

        // Masukkan data yang sudah difilter ke Excel
        $rowNum = 3; // mulai dari baris ke-2
        foreach ($filteredData as $row) {
            $colIndex = 1; // Mulai dari kolom 1 (kolom A)
            foreach ($row as $colValue) {
                // Masukkan data ke setiap kolom
                $newSheet->setCellValue($colIndex, $rowNum, $colValue);

                $colIndex++;
            }
            $rowNum++;
        }

        // Set headers untuk download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="filtered_data.xlsx"');
        header('Cache-Control: max-age=0');
        
        // Output file Excel ke browser
        $writer = new Xlsx($newSpreadsheet);
        $writer->save('php://output');
        exit;

    } else {
        echo "File Excel tidak ditemukan.";
    }
}
?>
