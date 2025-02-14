<?php
$filePath = __DIR__ . '/branding_JB_JR.xlsx'; // Path file Excel lo

if (file_exists($filePath)) {
    $fileContent = base64_encode(file_get_contents($filePath));
    echo json_encode(['file' => $fileContent]);
} else {
    echo json_encode(['error' => 'File not found']);
}
?>
