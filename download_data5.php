<?php
// Daftar file yang bisa didownload
$fileList = [
    'branding_JB_JR.xlsx',
    'branding_JT_DK_LP.xlsx',
    'gform_JB_JR.xlsx',
    'gform_JT_DK_LP.xlsx'
];

// Cek apakah user memilih file
if (isset($_GET['file']) && in_array($_GET['file'], $fileList)) {
    $filePath = $_GET['file'];

    // Cek apakah file ada
    if (file_exists($filePath)) {
        // Set header buat download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        die("File tidak ditemukan!");
    }
} else {
    die("File tidak valid!");
}
?>
