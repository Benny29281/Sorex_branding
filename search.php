<?php
// Include autoloader Google API & PhpSpreadsheet
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Load credentials JSON
$client = new Client();
$client->setAuthConfig('credentials.json'); // Ganti dengan path credentials
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);

$service = new Sheets($client);

// Spreadsheet ID & Range
$spreadsheetId = '1gn8kwSHxGMLhMlJZHydb02wmMrxUpZ_puAFfF1fYHu8'; // Ganti dengan ID Sheets lu
$range = 'Sheet1'; // Ambil semua data tanpa batasan kolom

// Fetch data dari Google Sheets
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Pastikan ada minimal 2 baris untuk header
$headers = isset($values[1]) ? $values[1] : [];
$dataRows = array_slice($values, 2);

$results = [];
$search = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = strtolower(trim($_POST['search']));
    foreach ($dataRows as $row) {
        $rowText = strtolower(implode(' ', $row));
        if (strpos($rowText, $search) !== false) {
            $results[] = $row;
        }
    }

    // Jika ada hasil pencarian, buat file Excel
    if (!empty($results)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $sheet->fromArray([$headers], NULL, 'A1');
        
        // Set data
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
    <title>Search Google Sheets</title>
    <style>

        .download-btn {
            display: inline-block;
            background-color:#e01432; /* Warna merah */
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }
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
            text-align: center;
            padding: 10px;
        }
        td {
            text-align: center;
            padding: 8px;
            background-color: #f8f8f8;
        }
        tr:nth-child(even) td {
            background-color: #e9f7e9;
        }
        tr:nth-child(odd) td {
            background-color: #ffffff;
        }
        .highlight {
            background-color: #ffd700;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <?php if (!empty($results)) : ?>
        <h2>Hasil Pencarian:</h2>
        <a href="hasil_pencarian.xlsx" download class="download-btn">Download Hasil Pencarian</a>

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
        <h2>Tidak ada data yang cocok!</h2>
    <?php endif; ?>
</body>
</html>
