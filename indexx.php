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
    background-color: #f4f4f4; /* Warna background halaman */
}

h1 {
    text-align: center;
    color: #333;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow tabel */
    background-color: #fff; /* Background tabel putih */
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: center;
}

th {
    background-color: #4CAF50; /* Hijau cerah */
    color: white;
    font-size: 16px;
    text-transform: uppercase;
}

tr:nth-child(even) td {
    background-color: #f9f9f9; /* Abu-abu muda */
}

tr:nth-child(odd) td {
    background-color: #ffffff; /* Putih */
}

td.highlight {
    background-color: #b4e197; /* Hijau muda untuk kolom yang di-highlight */
    font-weight: bold;
}

.edit-link {
    color: white;
    background-color: #28a745; /* Hijau untuk tombol */
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.edit-link:hover {
    background-color: #218838; /* Hijau lebih gelap saat hover */
}

.edit-form {
    margin-top: 20px;
    background-color: #fff; /* Putih untuk form */
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.edit-form h2 {
    color: #333;
    text-align: center;
}

.edit-form div {
    margin-bottom: 15px;
}

.edit-form label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.edit-form input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.edit-form button {
    background-color: #4CAF50; /* Hijau cerah */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.edit-form button:hover {
    background-color: #45a049; /* Hijau lebih gelap */
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
    foreach ($data as $rowIndex => $row) {
        echo "<tr>";
        foreach ($row as $colIndex => $cell) {
            $class = ($colIndex == 1) ? 'highlight' : ''; // Highlight kolom kedua (ubah sesuai kebutuhan)
            echo "<td class='{$class}'>{$cell}</td>";
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
