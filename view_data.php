<?php
require 'vendor/autoload.php'; // Pastikan path library-nya sesuai

use PhpOffice\PhpSpreadsheet\IOFactory;

$fileName = "data.xlsx";

if (file_exists($fileName)) {
    $spreadsheet = IOFactory::load($fileName);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(); // Ambil semua data di Excel jadi array
} else {
    $data = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Request Branding</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Request Branding</h1>

        <?php if (!empty($data)): ?>
            <table>
                <thead>
                    <tr>
                        <?php foreach ($data[0] as $header): ?>
                            <th><?= htmlspecialchars($header) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i < count($data); $i++): ?>
                        <tr>
                            <?php foreach ($data[$i] as $cell): ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <a href="download_excel.php" class="btn">Download Excel</a>
        <?php else: ?>
            <p>Tidak ada data tersedia.</p>
        <?php endif; ?>
    </div>
</body>
</html>
