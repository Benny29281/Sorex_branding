<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $request_id = isset($_POST['request_id']) ? trim((string) $_POST['request_id']) : '';
    $email = $_POST['email'];
    $area_sales = $_POST['area_sales'];
    $sales_code = $_POST['sales_code'];
    $store_name = $_POST['store_name'];
    $spv_name = $_POST['spv_name'];
    $location = $_POST['location'];
    $request_type = $_POST['request_type'];
    $branding_tools = $_POST['branding_tools'];
    $tools_size = $_POST['tools_size'];
    $quantity = $_POST['quantity'];
    $brand = $_POST['brand'];
    $delivery = $_POST['delivery'];
    $additional_notes = $_POST['additional_notes'];
    

    // Daftar file Excel yang akan diperbarui
    $files = ['gform_JT_DK_LP.xlsx'];
    $data_added = false;
    $updated_request_id = null;

    // Loop untuk mencari ID pada kedua file Excel
    foreach ($files as $file) {
        if (!file_exists($file)) {
            continue; // Skip jika file tidak ada
        }

        // Membaca file Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // Cari apakah ID sudah ada di dalam file
        $found = false;
        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Jika ID ditemukan, tandai bahwa ID sudah ada dan update data
            if (isset($rowData[0]) && (string) trim($rowData[0]) === (string) $request_id) {
                // Update data pada baris yang sesuai
                $rowIndex = $row->getRowIndex(); // Mengetahui baris ID ditemukan
                $sheet->setCellValue('A' . $rowIndex, $request_id); // Tetap menggunakan request_id yang sama
                $sheet->setCellValue('C' . $rowIndex, $email);
                $sheet->setCellValue('D' . $rowIndex, $area_sales);
                $sheet->setCellValue('E' . $rowIndex, $sales_code);
                $sheet->setCellValue('F' . $rowIndex, $store_name);
                $sheet->setCellValue('G' . $rowIndex, $spv_name);
                $sheet->setCellValue('H' . $rowIndex, $location);
                $sheet->setCellValue('I' . $rowIndex, $request_type);
                $sheet->setCellValue('J' . $rowIndex, $branding_tools);
                $sheet->setCellValue('K' . $rowIndex, $tools_size);
                $sheet->setCellValue('L' . $rowIndex, $quantity);
                $sheet->setCellValue('M' . $rowIndex, $brand);
                $sheet->setCellValue('N' . $rowIndex, $delivery);
                $sheet->setCellValue('O' . $rowIndex, $additional_notes);
                $sheet->setCellValue('P' . $rowIndex, "Revised: " . $request_id); // Menambahkan keterangan revisi

                // Tandai bahwa data telah berhasil diperbarui
                $updated_request_id = $request_id;
                $data_added = true;
                break; // Keluar dari loop setelah menemukan dan memperbarui data
            }
        }

        // Jika ID tidak ditemukan, cari baris kosong untuk menambahkan data baru
        if (!$found) {
            // Cari baris kosong terakhir untuk menambahkan data baru
            $rowIndex = $sheet->getHighestRow() + 1; // Mendapatkan baris kosong berikutnya

            // Tambahkan data baru di baris kosong
            $sheet->setCellValue('A' . $rowIndex, $request_id);  // Tetap menggunakan request_id yang sama
            $sheet->setCellValue('B' . $rowIndex, '');           // Biarkan kosong untuk kolom yang tidak perlu diisi
            $sheet->setCellValue('C' . $rowIndex, $email);
            $sheet->setCellValue('D' . $rowIndex, $area_sales);
            $sheet->setCellValue('E' . $rowIndex, $sales_code);
            $sheet->setCellValue('F' . $rowIndex, $store_name);
            $sheet->setCellValue('G' . $rowIndex, $spv_name);
            $sheet->setCellValue('H' . $rowIndex, $location);
            $sheet->setCellValue('I' . $rowIndex, $request_type);
            $sheet->setCellValue('J' . $rowIndex, $branding_tools);
            $sheet->setCellValue('K' . $rowIndex, $tools_size);
            $sheet->setCellValue('L' . $rowIndex, $quantity);
            $sheet->setCellValue('M' . $rowIndex, $brand);
            $sheet->setCellValue('N' . $rowIndex, $delivery);
            $sheet->setCellValue('O' . $rowIndex, $additional_notes);          

            // Tandai bahwa data baru telah ditambahkan
            $updated_request_id = $request_id;
            $data_added = true;
            break; // Keluar dari loop setelah berhasil menambahkan data
        }
    }

    // Jika data berhasil ditambahkan atau diperbarui, simpan perubahan ke file
    if ($data_added) {
        // Menyimpan perubahan ke file yang diperbarui
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);  // Menyimpan file yang telah diperbarui
        echo "Data berhasil disimpan $updated_request_id";
    } else {
        echo "Gagal menambahkan data!";
    }

     // Simpan log aktivitas
     $logFile = 'activity_log.txt';
     $dateTime = date("d F Y H:i:s");
     $id = isset($request_id) ? $request_id : 'Unknown';  // Pastikan ada ID atau fallback
     $email = isset($_POST['email']) ? $_POST['email'] : 'Unknown';
     $storeName = isset($_POST['store_name']) ? $_POST['store_name'] : 'Unknown Store';
     $request = 'Branding Request';  // Sesuaikan dengan jenis request yang ada, bisa diubah dinamis
 
     // Format log sesuai dengan kebutuhan
     $logMessage = "{$dateTime} | ID: {$id} | Email: {$email} | Request: {$request} | Toko: {$storeName}\n";
 
     // Simpan ke file log
     file_put_contents($logFile, $logMessage, FILE_APPEND);

    // Arahkan kembali setelah proses selesai
    echo "<script>
        alert('Data berhasil Direvisi $updated_request_id');
        window.location.href = 'user.html'; // Ganti dengan URL menu user
    </script>";
}
?>
