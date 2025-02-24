<?php
// Include autoloader Google API & PhpSpreadsheet
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Setup Google Client
$client = new Client();
$client->setAuthConfig('credentials.json'); // Pastikan path-nya bener
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);

$service = new Sheets($client);

// Array Spreadsheet ID lo
$spreadsheetIds = [
    '1gn8kwSHxGMLhMlJZHydb02wmMrxUpZ_puAFfF1fYHu8',
    '1Lq436zcFSmbx9a-Tv2dLLn5es8rX0G4u4vML1_UWdQI'
];
$range = 'Sheet1'; // Range yang mau lo ambil

$allData = [];
$headers = null;

// Loop tiap spreadsheet dan merge data
foreach ($spreadsheetIds as $id) {
    $response = $service->spreadsheets_values->get($id, $range);
    $values = $response->getValues();
    if (!empty($values)) {
        // Ambil header dari file pertama
        if (!$headers) {
            $headers = $values[0]; // asumsinya header di baris pertama
        }
        // Ambil data rows, skip header tiap file
        $dataRows = array_slice($values, 1);
        $allData = array_merge($allData, $dataRows);
    } else {
        die("Data ga ada di Spreadsheet ID: {$id}!");
    }
}

// Proses pencarian dari form
$results = [];
$search = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = strtolower(trim($_POST['search']));
    foreach ($allData as $row) {
        $rowText = strtolower(implode(' ', $row));
        if (strpos($rowText, $search) !== false) {
            $results[] = $row;
        }
    }

    // Jika ada hasil, buat file Excel
    if (!empty($results)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->fromArray([$headers], NULL, 'A1');
        // Set data hasil pencarian
        $sheet->fromArray($results, NULL, 'A2');

        $writer = new Xlsx($spreadsheet);
        $filename = 'hasil_pencarian.xlsx';
        $writer->save($filename);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="bar2.png" type="image/png">
    <title>Search Google Sheets</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
        }
        td {
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Cari Data Google Sheets</h2>
    <?php if (!empty($results)) : ?>
        <h3>Hasil Pencarian:</h3>
        <a href="hasil_pencarian.xlsx" download>Download Hasil Pencarian</a>
        <table>
            <tr>
                <?php foreach ($headers as $header) : ?>
                    <th><?php echo htmlspecialchars($header); ?></th>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($results as $row) : ?>
                <tr>
                    <?php for ($i = 0; $i < count($headers); $i++) : ?>
                        <td><?php echo isset($row[$i]) ? htmlspecialchars($row[$i]) : ''; ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
        <h3>Tidak ada data yang cocok!</h3>
    <?php endif; ?>
</body>
</html>
