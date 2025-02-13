<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

$files = ['data.xlsx', 'data4.xlsx']; // File Excel yang dicek
$data_found = false;
$data = [];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "ID tidak boleh kosong"]);
    exit;
}

$requestId = trim($_GET['id']);

foreach ($files as $file) {
    if (!file_exists($file)) {
        continue;
    }

    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    foreach ($sheet->getRowIterator() as $rowIndex => $row) {
        if ($rowIndex == 1) continue;

        $rowData = [];
        foreach ($row->getCellIterator() as $cell) {
            $rowData[] = trim($cell->getValue());
        }

        // Cek ID di kolom kedua (index ke-1)
        if (isset($rowData[1]) && $rowData[1] == $requestId) {
            $data = [
                "email" => $rowData[2] ?? '',
                "area_sales" => $rowData[3] ?? '',
                "sales_code" => $rowData[4] ?? '',
                "store_name" => $rowData[5] ?? '',
                "spv_name" => $rowData[6] ?? '',
                "location" => $rowData[7] ?? '',
                "request_type" => $rowData[8] ?? '',
                "branding_tools" => $rowData[9] ?? '',
                "tools_size" => $rowData[10] ?? '',
                "quantity" => $rowData[11] ?? '',
                "brand" => $rowData[12] ?? '',
                "delivery" => $rowData[13] ?? '',
                "additional_notes" => $rowData[14] ?? ''
            ];
            $data_found = true;
            break 2;
        }
    }
}

if ($data_found) {
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Data tidak ditemukan"]);
}
?>
