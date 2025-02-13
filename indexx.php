<?php
require 'vendor/autoload.php'; // Memuat autoloader Composer untuk PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fungsi untuk mengupdate data Excel
function updateExcelCell($file, $rowIndex, $colIndex, $newValue) {
    // Muat file Excel
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    // Update nilai pada sel yang ditentukan
    $sheet->setCellValue($colIndex + 1, $rowIndex + 1, $newValue);

    // Simpan perubahan ke file Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save($file);
}

// Path ke file Excel
$file = 'gform_JB_JR.xlsx';

// Cek apakah file Excel ada
if (!file_exists($file)) {
    die("File Excel tidak ditemukan.");
}

// Membaca file Excel
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

// Proses jika ada form submit untuk mengedit data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data yang diubah dari form
    $rowIndex = intval($_POST['row_index']);
    $colIndex = intval($_POST['col_index']);
    $newValue = $_POST['new_value'];

    // Validasi input
    if (trim($newValue) === '') {
        die("Nilai tidak boleh kosong.");
    }

    // Panggil fungsi untuk mengupdate Excel
    updateExcelCell($file, $rowIndex, $colIndex, $newValue);

    echo "<script>alert('Data berhasil diperbarui!');</script>";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Excel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .edit-link {
            color: white;
            background-color: #28a745;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .edit-link:hover {
            background-color: #218838;
        }
        .edit-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>Data Excel</h1>

<!-- Tabel untuk menampilkan data Excel -->
<table>
    <thead>
        <tr>
            <?php
            // Menampilkan header tabel
            $headers = $sheet->rangeToArray('A1:Z1')[0];
            foreach ($headers as $header) {
                echo "<th>{$header}</th>";
            }
            ?>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Menampilkan baris data Excel
        foreach ($data as $rowIndex => $row) {
            echo "<tr>";
            foreach ($row as $colIndex => $cell) {
                echo "<td>{$cell}</td>";
            }
            echo "<td><a href='?edit={$rowIndex}' class='edit-link'>Edit</a></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<!-- Form untuk mengedit data -->
<?php
if (isset($_GET['edit'])) {
    $rowIndex = intval($_GET['edit']);
    $editRow = $data[$rowIndex];
    ?>
    <div class="edit-form">
        <h2>Edit Data Baris <?= $rowIndex + 1 ?></h2>
        <form method="POST">
            <input type="hidden" name="row_index" value="<?= $rowIndex ?>">
            <?php foreach ($editRow as $colIndex => $value): ?>
                <div>
                    <label>Kolom <?= $colIndex + 1 ?> (<?= htmlspecialchars($headers[$colIndex]) ?>):</label>
                    <input type="text" name="new_value" value="<?= htmlspecialchars($value) ?>" required>
                    <input type="hidden" name="col_index" value="<?= $colIndex ?>">
                    <button type="submit">Simpan Kolom <?= $colIndex + 1 ?></button>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
    <?php
}
?>

</body>
</html>
