<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();

if (!isset($_GET['id']) && !isset($_SESSION['edit_id'])) {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href = 'user.html';</script>";
    exit();
}

$idToEdit = $_GET['id'] ?? $_SESSION['edit_id'];
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
$headers = $dataArray[0];
$rowData = null;

foreach ($dataArray as $row) {
    if ($row[0] === $idToEdit) { // Kolom pertama adalah ID
        $rowData = $row;
        break;
    }
}

if (!$rowData) {
    echo "<script>alert('Data dengan ID $idToEdit tidak ditemukan!'); window.location.href = 'user.html';</script>";
    exit();
}

// Tampilkan data di form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
</head>
<body>
    <h1>Edit Data</h1>
    <form action="update.php" method="post" enctype="multipart/form-data">
        <?php
        foreach ($headers as $index => $header) {
            $value = htmlspecialchars($rowData[$index], ENT_QUOTES);
            echo "
                <label for='$header'>$header:</label><br>
                <input type='text' id='$header' name='".strtolower(str_replace(' ', '_', $header))."' value='$value'><br><br>
            ";
        }
        ?>
        <button type="submit">Update</button>
    </form>
</body>
</html>
