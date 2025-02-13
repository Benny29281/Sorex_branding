<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileName = "data4.xlsx";

    if (!file_exists($fileName)) {
        echo "<script>alert('File data tidak ditemukan!'); window.location.href = 'user.html';</script>";
        exit();
    }

    // Load file Excel
    $spreadsheet = IOFactory::load($fileName);
    $sheet = $spreadsheet->getActiveSheet();
    $dataArray = $sheet->toArray();

    // Cari data berdasarkan ID
    $idToUpdate = $_POST['id'];
    foreach ($dataArray as $index => $row) {
        if ($row[0] === $idToUpdate) {
            // Update data di baris tersebut
            foreach ($_POST as $key => $value) {
                $colIndex = array_search(ucwords(str_replace('_', ' ', $key)), $dataArray[0]);
                if ($colIndex !== false) {
                    $sheet->setCellValue($colIndex + 1, $index + 1, $value);
                }
            }
            break;
        }
    }

    // Simpan file Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);

    echo "<script>
        alert('Data berhasil diperbarui!');
        window.location.href = 'user.html'; // Kembali ke menu user
    </script>";
}
?>
