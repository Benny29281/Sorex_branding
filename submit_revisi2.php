<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $request_id     = isset($_POST['request_id']) ? trim((string) $_POST['request_id']) : '';
    $email          = $_POST['email'];
    $area_sales     = $_POST['area_sales'];
    $sales_code     = $_POST['sales_code'];
    $store_name     = $_POST['store_name'];
    $spv_name       = $_POST['spv_name'];
    $location       = $_POST['location'];
    $request_type   = $_POST['request_type'];
    $branding_tools = $_POST['branding_tools'];
    $tools_size     = $_POST['tools_size'];
    $quantity       = $_POST['quantity'];
    $brand          = $_POST['brand'];
    $delivery       = $_POST['delivery'];
    $additional_notes = $_POST['additional_notes'];

    // Proses upload foto
    $uploadDir = "uploads/";
    $baseUrl = "http://localhost/Branding/uploads/"; // Ganti sesuai domain lo
    $installationPhotoLink = "";
    $designPhotoLink = "";

    // Upload foto area pemasangan
    if (isset($_FILES['installation_photo']) && $_FILES['installation_photo']['error'] === UPLOAD_ERR_OK) {
        $installationPhotoName = basename($_FILES['installation_photo']['name']);
        $installationPhotoPath = $uploadDir . $installationPhotoName;
        if (move_uploaded_file($_FILES['installation_photo']['tmp_name'], $installationPhotoPath)) {
            $installationPhotoLink = $baseUrl . $installationPhotoName;
        }
    }
    // Upload foto sugest design
    if (isset($_FILES['design_photo']) && $_FILES['design_photo']['error'] === UPLOAD_ERR_OK) {
        $designPhotoName = basename($_FILES['design_photo']['name']);
        $designPhotoPath = $uploadDir . $designPhotoName;
        if (move_uploaded_file($_FILES['design_photo']['tmp_name'], $designPhotoPath)) {
            $designPhotoLink = $baseUrl . $designPhotoName;
        }
    }
    
    // Buat formula hyperlink untuk foto, kalau ada
    $installationPhotoCell = $installationPhotoLink ? '=HYPERLINK("' . $installationPhotoLink . '", "Lihat Foto")' : '';
    $designPhotoCell = $designPhotoLink ? '=HYPERLINK("' . $designPhotoLink . '", "Lihat Foto")' : '';

    // Tentukan keterangan revisi (bisa juga tambahkan revisi ke berapa, misalnya)
    $revision_note = "Revised: " . $request_id;

    // Susun data revisi sesuai urutan kolom di spreadsheet
    // Kolom:
    // A: request_id, B: kosong, C: email, D: area_sales, E: sales_code,
    // F: store_name, G: spv_name, H: location, I: request_type, J: branding_tools,
    // K: tools_size, L: quantity, M: brand, N: delivery, O: additional_notes,
    // P: keterangan revisi, Q: foto area pemasangan, R: foto sugest design
    $rowData = [
        $request_id,
        '', // Kolom B kosong
        $email,
        $area_sales,
        $sales_code,
        $store_name,
        $spv_name,
        $location,
        $request_type,
        $branding_tools,
        $tools_size,
        $quantity,
        $brand,
        $delivery,
        $additional_notes,
        $revision_note,
        $installationPhotoCell,
        $designPhotoCell
    ];

    // Inisialisasi Google Client
    $client = new Client();
    $client->setAuthConfig('credentials.json'); // Pastikan path credentials.json bener
    $client->setScopes([Sheets::SPREADSHEETS]);
    $service = new Sheets($client);

    $spreadsheetId = '1liQU4eJI0Y-9yW0ZKFh8nAcD1VkgAk77WVlLUbjvB7M'; // Ganti dengan ID spreadsheet lo
    $range = 'Sheet1!A:R';  // Data ada di kolom A sampai R (18 kolom)

    // Karena revisi harus selalu ditambahkan sebagai baris baru,
    // langsung panggil append tanpa mengecek apakah request_id sudah ada.
    $body = new Sheets\ValueRange([
        'values' => [$rowData]
    ]);
    $params = ['valueInputOption' => 'USER_ENTERED'];
    $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    $updated_request_id = $request_id;

    // Simpan log aktivitas
    $logFile = 'activity_log2.txt';
    $dateTime = date("d F Y H:i:s");
    $logMessage = "{$dateTime} | ID: {$request_id} | Email: {$email} | Request: Branding Request | Toko: {$store_name}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);

    // Tampilkan alert dan redirect ke halaman user
    echo "<script>
        alert('Data revisi berhasil ditambahkan untuk ID $updated_request_id');
        window.location.href = 'user2.html';
    </script>";
}
?>
