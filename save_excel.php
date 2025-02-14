<?php
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['file'])) {
    $filePath = __DIR__ . '/branding_JB_JR.xlsx'; // Path file Excel lo

    // Decode Base64 string dan simpan ke file Excel
    $fileContent = base64_decode($data['file']);
    file_put_contents($filePath, $fileContent);

    echo json_encode(['status' => 'success', 'message' => 'File berhasil disimpan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file data received']);
}
?>
