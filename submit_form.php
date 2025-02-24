<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Setup Google Client buat akses Sheets API
$client = new Client();
$client->setAuthConfig('credentials.json'); // Pastikan path-nya bener
$client->setScopes([Sheets::SPREADSHEETS]);
$service = new Sheets($client);

$spreadsheetId = '1liQU4eJI0Y-9yW0ZKFh8nAcD1VkgAk77WVlLUbjvB7M'; // Ganti sama ID spreadsheet lo
$range = 'Sheet1!A1:Z'; // Range sesuai sheet lo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate Request ID
    $lastIdFile = 'last_id2.txt';
    $lastId = 630; // Default, bisa diubah sesuai kebutuhan
    $prefix = 'BS';

    if (file_exists($lastIdFile)) {
        $lastId = (int)file_get_contents($lastIdFile);
    }
    $newId = $prefix . $lastId;
    $lastId++;
    file_put_contents($lastIdFile, $lastId);

    $tanggalSubmit = date("d F Y");

    // Proses upload foto
    $uploadDir = "uploads/";
    $baseUrl = "http://localhost/Branding/uploads/"; // Ganti sesuai domain lo
    $installationPhotoLink = "";
    $designPhotoLink = "";

    if (isset($_FILES['installation_photo']) && $_FILES['installation_photo']['error'] === UPLOAD_ERR_OK) {
        $installationPhotoName = basename($_FILES['installation_photo']['name']);
        $installationPhotoPath = $uploadDir . $installationPhotoName;
        move_uploaded_file($_FILES['installation_photo']['tmp_name'], $installationPhotoPath);
        $installationPhotoLink = $baseUrl . $installationPhotoName;
    }
    
    if (isset($_FILES['design_photo']) && $_FILES['design_photo']['error'] === UPLOAD_ERR_OK) {
        $designPhotoName = basename($_FILES['design_photo']['name']);
        $designPhotoPath = $uploadDir . $designPhotoName;
        move_uploaded_file($_FILES['design_photo']['tmp_name'], $designPhotoPath);
        $designPhotoLink = $baseUrl . $designPhotoName;
    }

    // Gabungin input dropdown "branding tools" kalo pilih "Lainnya"
    $brandingTools = $_POST['branding_tools'];
    $otherTools = trim($_POST['other_tools']);
    if ($brandingTools === 'Lainnya' && !empty($otherTools)) {
        $brandingTools .= " - " . $otherTools;
    }

    // Bikin formula HYPERLINK buat foto, biar bisa diklik langsung dari Google Sheets
    $installationPhotoCell = $installationPhotoLink ? '=HYPERLINK("' . $installationPhotoLink . '", "Lihat Foto")' : '';
    $designPhotoCell = $designPhotoLink ? '=HYPERLINK("' . $designPhotoLink . '", "Lihat Foto")' : '';

    // Data yang mau dikirim ke Google Sheets
    $data = [
        strtoupper($tanggalSubmit),
        strtoupper($newId),
        strtoupper($_POST['email']),
        strtoupper($_POST['area_sales']),
        strtoupper($_POST['sales_code']),
        strtoupper($_POST['spv_name']),
        strtoupper($_POST['store_name']),
        strtoupper($_POST['location']),
        strtoupper($_POST['request_type']),
        strtoupper($brandingTools),
        strtoupper($_POST['tools_size']),
        strtoupper($_POST['quantity']),
        strtoupper($_POST['brand']),
        strtoupper($_POST['delivery']),
        strtoupper($_POST['additional_notes'] ?? 'N/A'),
        $installationPhotoCell,
        $designPhotoCell
    ];

    // Append data ke spreadsheet pake USER_ENTERED buat nge-proses formula
    $body = new Sheets\ValueRange([
        'values' => [$data]
    ]);
    $params = ['valueInputOption' => 'USER_ENTERED'];
    $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

    // Log aktivitas dengan format yang lo minta
    $logFile = 'activity_log2.txt';
    $dateTime = date("d F Y H:i:s");
    $email = $_POST['email'] ?? 'Unknown';
    $storeName = $_POST['store_name'] ?? 'Unknown Store';
    $request = 'Branding Request';

    $logMessage = "{$dateTime} | ID: {$newId} | Email: {$email} | Request: {$request} | Toko: {$storeName}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);


    // Kirim email ke user dengan no ID request
    $to = $_POST['email'];
    $subject = "Branding Request ID - " . $newId;
    $message = "Halo,\n\nTerima kasih udah ngirim form branding. Request ID lo adalah: " . $newId . ".\n\nKalo ada pertanyaan, langsung aja hubungi Admin ya.\n\nSalam,\nTim Branding";
    $headers = "From: no-reply@yourdomain.com\r\n" .
               "Reply-To: support@yourdomain.com\r\n" .
               "Content-Type: text/plain; charset=UTF-8";

    // Cek apakah email berhasil dikirim
    if (mail($to, $subject, $message, $headers)) {
        $emailStatus = "Email berhasil dikirim.";
    } else {
        $emailStatus = "Gagal ngirim email.";
    }

    // Tampilkan alert modal setelah submit
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var id = '" . $newId . "';
            var alertDiv = document.createElement('div');
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '50%';
            alertDiv.style.left = '50%';
            alertDiv.style.transform = 'translate(-50%, -50%)';
            alertDiv.style.backgroundColor = '#fff';
            alertDiv.style.border = '1px solid #ddd';
            alertDiv.style.padding = '20px';
            alertDiv.style.borderRadius = '8px';
            alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
            alertDiv.style.textAlign = 'center';
            alertDiv.style.zIndex = '1000';
            alertDiv.style.fontFamily = 'Arial, sans-serif';

            alertDiv.innerHTML = `
                <p style='margin-bottom: 15px; font-size: 16px;'>Request ID kamu adalah:</p>
                <strong style='font-size: 20px; color: #007BFF;'>` + id + `</strong>
                <p style='margin-top:10px; font-size:14px;'>` + '" . $emailStatus . "' + `</p>
                <div style='margin-top: 15px;'>
                    <button id='copyButton' style='background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; margin-right: 10px;'>Copy ID</button>
                    <button id='closeButton' style='background-color: #dc3545; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;'>Close</button>
                </div>
            `;
            document.body.appendChild(alertDiv);

            // Fungsi copy ID ke clipboard
            document.getElementById('copyButton').addEventListener('click', function() {
                navigator.clipboard.writeText(id).then(function() {
                    alert('ID berhasil disalin ke clipboard!');
                }).catch(function(err) {
                    alert('Gagal menyalin ID!');
                });
            });

            // Fungsi close alert dan redirect ke user.html
            document.getElementById('closeButton').addEventListener('click', function() {
                alertDiv.remove();
                window.location.href = 'user2.html';
            });
        });
    </script>";
}
?>
