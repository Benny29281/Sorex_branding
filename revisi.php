<?php
require 'vendor/autoload.php'; // Pastikan file autoload ada

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['request_id'])) {
    // Ambil ID dari form
    $requestId = $_POST['request_id'];

    // File Excel yang akan dibaca
    $files = ['gform_JB_JR.xlsx'];
    $data_found = false;
    $data = [];

    // Loop untuk membaca file Excel
    foreach ($files as $file) {
        if (!file_exists($file)) {
            continue; // Skip jika file tidak ada
        }

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            if ($rowIndex == 1) continue; // Lewatin header (baris pertama)

            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Check apakah ID di kolom kedua (index 1)
            if ($rowData[1] == $requestId) {
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
                break 2; // Break dari loop ketika data ditemukan
            }
        }
    }

    if (!$data_found) {
        $data = ["error" => "Data tidak ditemukan untuk ID: " . $requestId];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Revisi Data</title>
    <link rel="icon" href="bar2.png" type="image/png">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .container { max-width: 600px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color:rgb(247, 41, 75); color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color:rgb(255, 58, 84); }
    </style>
</head>
<body>

<div class="container">
    <h1>Form Revisi Data</h1>

    <form method="POST" action="">
        <label for="request_id">Masukkan ID Request untuk Mencari Data:</label>
        <input type="text" id="request_id" name="request_id" placeholder="Masukkan ID Request" required>
        
        <button type="submit">Cari Data</button>
    </form>

    <?php if (isset($data['error'])): ?>
        <p style="color: red; text-align: center;"><?php echo $data['error']; ?></p>
    <?php endif; ?>

    <?php if (isset($data_found) && $data_found): ?>

        <form method="POST" action="submit_revisi.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>

            <label for="area_sales">Area Sales:</label>
            <select id="area_sales" name="area_sales" required>
                <option value="JB" <?php echo ($data['area_sales'] == 'JB') ? 'selected' : ''; ?>>JB</option>
                <option value="JR" <?php echo ($data['area_sales'] == 'JR') ? 'selected' : ''; ?>>JR</option>
            </select>

            <label for="sales_code">Nama Sales:</label>
            <input type="text" id="sales_code" name="sales_code" value="<?php echo htmlspecialchars($data['sales_code']); ?>" required>

            <label for="store_name">Nama Toko:</label>
            <input type="text" id="store_name" name="store_name" value="<?php echo htmlspecialchars($data['store_name']); ?>" required>

            <label for="spv_name">Nama SPV:</label>
            <input type="text" id="spv_name" name="spv_name" value="<?php echo htmlspecialchars($data['spv_name']); ?>" required>

            <label for="location">Lokasi:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($data['location']); ?>" required>

            <label for="request_type">Jenis Permintaan:</label>
            <select id="request_type" name="request_type" required>
                <option value="BARU" <?php echo ($data['request_type'] == 'BARU') ? 'selected' : ''; ?>>Baru</option>
                <option value="PERMAJAAN" <?php echo ($data['request_type'] == 'PERMAJAAN') ? 'selected' : ''; ?>>Permajaan</option>
            </select>

            <label for="branding_tools">Jenis Tools Branding:</label>
            <select id="branding_tools" name="branding_tools" required>
                <option value="">-- Pilih Tools --</option>
                <option value="spanduk_korea"<?php echo ($data['branding_tools'] == 'spanduk_korea') ? 'selected' : ''; ?>>SPANDUK KOREA</option>
                <option value="spanduk_korea_mata_ayam"<?php echo ($data['branding_tools'] == 'spanduk_korea_mata_ayam') ? 'selected' : ''; ?>>SPANDUK KOREA (PAKAI MATA AYAM)</option>
                <option value="spanduk_china"<?php echo ($data['branding_tools'] == 'spanduk_china') ? 'selected' : ''; ?>>SPANDUK CHINA</option>
                <option value="spanduk_china_mata_ayam"<?php echo ($data['branding_tools'] == 'spanduk_china_mata_ayam') ? 'selected' : ''; ?>>SPANDUK CHINA (PAKAI MATA AYAM)</option>
                <option value="poster_doff"<?php echo ($data['branding_tools'] == 'poster_doff') ? 'selected' : ''; ?>>POSTER DOFF</option>
                <option value="poster_glossy"<?php echo ($data['branding_tools'] == 'poster_glossy') ? 'selected' : ''; ?>>POSTER GLOSSY</option>
                <option value="stiker_doff"<?php echo ($data['branding_tools'] == 'stiker_doff') ? 'selected' : ''; ?>>STIKER DOFF</option>
                <option value="stiker_glossy"<?php echo ($data['branding_tools'] == 'stiker_glossy') ? 'selected' : ''; ?>>STIKER GLOSSY</option>
                <option value="stiker_oneway"<?php echo ($data['branding_tools'] == 'stiker_oneway') ? 'selected' : ''; ?>>STIKER ONEWAY</option>
                <option value="stiker_backlite"<?php echo ($data['branding_tools'] == 'stiker_backlite') ? 'selected' : ''; ?>>STIKER BACKLITE (UNTUK NEONBOX)</option>
                <option value="stiker_overprint"<?php echo ($data['branding_tools'] == 'stiker_overprint') ? 'selected' : ''; ?>>STIKER OVERPRINT (UNTUK NEONBOX)</option>
                <option value="akrilik_kapur"<?php echo ($data['branding_tools'] == 'akrilik_kapur') ? 'selected' : ''; ?>>AKRILIK KAPUR / PVC</option>
                <option value="akrilik_bening"<?php echo ($data['branding_tools'] == 'akrilik_bening') ? 'selected' : ''; ?>>AKRILIK BENING</option>
                <option value="impraboard"<?php echo ($data['branding_tools'] == 'impraboard') ? 'selected' : ''; ?>>IMPRABOARD</option>
                <option value="neonbox"<?php echo ($data['branding_tools'] == 'neonbox') ? 'selected' : ''; ?>>NEONBOX</option>
                <option value="art_paper_pop"<?php echo ($data['branding_tools'] == 'art_paper_pop') ? 'selected' : ''; ?>>ART PAPER / POP</option>
                <option value="x_banner"<?php echo ($data['branding_tools'] == 'x_banner') ? 'selected' : ''; ?>>X BANNER</option>
                <option value="roll_up_banner"<?php echo ($data['branding_tools'] == 'roll_up_banner') ? 'selected' : ''; ?>>ROLL UP BANNER</option>
                <option value="tripod_banner"<?php echo ($data['branding_tools'] == 'tripod_banner') ? 'selected' : ''; ?>>TRIPOD BANNER</option>
                <label for="other_tools">Lainnya (Opsional):</label>
            <textarea id="other_tools" name="other_tools" placeholder="Masukkan jenis tools lainnya..."></textarea>
            </select>

            <label for="tools_size">Ukuran Tools Branding:</label>
            <input type="text" id="tools_size" name="tools_size" value="<?php echo htmlspecialchars($data['tools_size']); ?>" required>

            <label for="quantity">QTY Tools:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($data['quantity']); ?>" required>

            <label for="brand">Brand:</label>
            <select id="brand" name="brand" required>
                <option value="">-- Pilih Brand --</option>
                <option value="SOREX MAN"<?php echo ($data['brand'] == 'sorex_man') ? 'selected' : ''; ?>>SOREX MAN</option>
                <option value="SOREX LADIES"<?php echo ($data['brand'] == 'sorex_ladies') ? 'selected' : ''; ?>>SOREX LADIES</option>
                <option value="SOREX KIDS"<?php echo ($data['brand'] == 'sorex_kids') ? 'selected' : ''; ?>>SOREX KIDS</option>
            </select>

            <label for="delivery">Pengiriman:</label>
            <input type="text" id="delivery" name="delivery" value="<?php echo htmlspecialchars($data['delivery']); ?>" required>

            <label for="additional_notes">Keterangan Tambahan:</label>
            <textarea id="additional_notes" name="additional_notes"><?php echo htmlspecialchars($data['additional_notes']); ?></textarea>

            <button type="submit">Kirim Revisi</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
