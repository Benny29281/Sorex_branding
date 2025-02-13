<?php
$fileName = "gform_JT_DK_LP.xlsx";

// Cek apakah file Excel ada
if (file_exists($fileName)) {
    // Set header buat download file
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=" . basename($fileName));
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");
    header("Content-Length: " . filesize($fileName));

    // Baca file dan kirim ke user
    readfile($fileName);
    exit;
} else {
    echo "File tidak ditemukan.";
}
?>
