<?php
require 'vendor/autoload.php'; // Pastikan path library-nya sesuai

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lastIdFile = 'last_id.txt';
    $lastId = 530;  // Default ID mulai dari 600
    $prefix = 'RB';

    if (file_exists($lastIdFile)) {
        $lastId = (int)file_get_contents($lastIdFile);
    }

    $newId = $prefix . $lastId;
    $lastId++;
    file_put_contents($lastIdFile, $lastId);

    $tanggalSubmit = date("d F Y");

    $uploadDir = "uploads/";
    $baseUrl = "http://localhost/Branding/uploads/"; // Ganti dengan domain atau IP server lu
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

    $brandingTools = $_POST['branding_tools'];
    $otherTools = trim($_POST['other_tools']); // Ambil input dari other_tools (kalau ada)
    
    // Gabungkan kalau user pilih "Lainnya" di dropdown
    if ($brandingTools === 'Lainnya' && !empty($otherTools)) {
        $brandingTools .= " - " . $otherTools; // Tambahin input ke teks "Lainnya"
    }

    $data = [
        'Tanggal Submit' => strtoupper($tanggalSubmit),
        'ID' => strtoupper($newId),
        'Email' => strtoupper($_POST['email']),
        'Area Sales' => strtoupper($_POST['area_sales']),
        'Nama Sales' => strtoupper($_POST['sales_code']),
        'Nama SPV' => strtoupper($_POST['spv_name']),
        'Nama Toko' => strtoupper($_POST['store_name']),
        'Lokasi' => strtoupper($_POST['location']),
        'Jenis Permintaan' => strtoupper($_POST['request_type']),
        'Tools Branding' => strtoupper($brandingTools),
        'Ukuran Tools' => strtoupper($_POST['tools_size']),
        'Qty' => strtoupper($_POST['quantity']),
        'Brand' => strtoupper($_POST['brand']),
        'Pengiriman' => strtoupper($_POST['delivery']),
        'Keterangan Tambahan' => strtoupper(isset($_POST['additional_notes']) && !empty($_POST['additional_notes']) ? $_POST['additional_notes'] : 'N/A'),
        'Foto Area Pemasangan' => strtoupper($installationPhotoLink),
        'Foto Sugest Desain' => strtoupper($designPhotoLink)

    ];

    $fileName = "gform_JB_JR.xlsx";
    if (file_exists($fileName)) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileName);
        $sheet = $spreadsheet->getActiveSheet();
    } else {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(array_keys($data), NULL, 'A1');
    }

    $row = $sheet->getHighestRow() + 1;
    $sheet->fromArray(array_values($data), NULL, 'A' . $row);

    if ($installationPhotoLink) {
        $sheet->setCellValue('P' . $row, 'Lihat Foto');
        $sheet->getCell('P' . $row)->getHyperlink()->setUrl($installationPhotoLink);
    }
    
    if ($designPhotoLink) {
        $sheet->setCellValue('Q' . $row, 'Lihat Foto');
        $sheet->getCell('Q' . $row)->getHyperlink()->setUrl($designPhotoLink);
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);

    $logFile = 'activity_log.txt';
    $dateTime = date("d F Y H:i:s");
    $email = $_POST['email'] ?? 'Unknown';
    $storeName = $_POST['store_name'] ?? 'Unknown Store';
    $request = 'Branding Request';

    $logMessage = "{$dateTime} | ID: {$newId} | Email: {$email} | Request: {$request} | Toko: {$storeName}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);

    $to = $_POST['email'];
    $subject = 'Branding Request ID - ' . $newId;
    $message = "
        Halo,

        Terima kasih telah mengirim permintaan branding. Berikut adalah ID permintaan Anda: $newId.

        Jika Anda memiliki pertanyaan atau perlu bantuan lebih lanjut, silakan hubungi kami.

        Terima kasih,
        Tim Branding
    ";
    $headers = "From: no-reply@gmail.com" . "\r\n" .
               "Reply-To: support@gmail.com" . "\r\n" .
               "Content-Type: text/plain; charset=UTF-8";

    if (mail($to, $subject, $message, $headers)) {
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
                <strong style='font-size: 20px; color: #007BFF;'>" . $newId . "</strong>
                <div style='margin-top: 15px;'>
                    <button id='copyButton' style='background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; margin-right: 10px;'>Copy ID</button>
                    <button id='closeButton' style='background-color: #dc3545; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;'>Close</button>
                </div>
            `;
            document.body.appendChild(alertDiv);

            // Copy to clipboard
            document.getElementById('copyButton').addEventListener('click', function() {
                navigator.clipboard.writeText(id).then(function() {
                    alert('ID berhasil disalin ke clipboard!');
                }).catch(function(err) {
                    alert('Gagal menyalin ID!');
                });
            });

            // Close alert
            document.getElementById('closeButton').addEventListener('click', function() {
                alertDiv.remove();
                window.location.href = 'user.html';
            });
        });
    </script>";
} else {
    echo "<script>
        alert('Gagal mengirim email, coba lagi!');
        window.location.href = 'user2.html';
    </script>";
}

}
