<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$host = "localhost";
$user = "root";
$password = "";
$db = "branding";

// Koneksi ke database
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Filter berdasarkan tanggal dan nama sales
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$salesName = isset($_GET['nama_sales']) ? $_GET['nama_sales'] : null;

$query = "SELECT * FROM requests WHERE 1=1";
if ($startDate && $endDate) {
    $query .= " AND tanggal BETWEEN '$startDate' AND '$endDate'";
}
if ($salesName) {
    $query .= " AND nama_sales = '$salesName'";
}

$result = $conn->query($query);

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'Tanggal');
$sheet->setCellValue('B1', 'Nama Sales');
$sheet->setCellValue('C1', 'Request Code');
$sheet->setCellValue('D1', 'Nama Toko');
$sheet->setCellValue('E1', 'Area');
$sheet->setCellValue('F1', 'Tipe');
$sheet->setCellValue('G1', 'Via');
$sheet->setCellValue('H1', 'Permintaan');
$sheet->setCellValue('I1', 'QTY');

// Isi data dari database
$row = 2;
while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $data['tanggal']);
    $sheet->setCellValue('B' . $row, $data['nama_sales']);
    $sheet->setCellValue('C' . $row, $data['request_code']);
    $sheet->setCellValue('D' . $row, $data['nama_toko']);
    $sheet->setCellValue('E' . $row, $data['area']);
    $sheet->setCellValue('F' . $row, $data['tipe']);
    $sheet->setCellValue('G' . $row, $data['via']);
    $sheet->setCellValue('H' . $row, $data['permintaan']);
    $sheet->setCellValue('I' . $row, $data['qty']);
    $row++;
}

// Buat file Excel
$filename = "Filtered_Data.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Tutup koneksi
$conn->close();
exit;
?>
