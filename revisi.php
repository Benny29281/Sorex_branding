<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$data_found = false;
$data = [];

// Inisialisasi Google Client
$client = new Client();
$client->setAuthConfig('credentials.json'); // Pastikan path bener
$client->setScopes([Sheets::SPREADSHEETS]);
$service = new Sheets($client);

// Ganti spreadsheet ID dan range sesuai kebutuhan lo
$spreadsheetId = '1bgD6itsQmFsEU76RhdVsWmd8qmMcyFAKcVGkuwC82tk';
$range = 'Sheet1!A1:Z'; // Asumsi header ada di baris 1

if (isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];

    // Ambil data dari Google Sheets
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (!empty($values)) {
        // Loop mulai dari baris kedua (index 1) karena baris pertama header
        for ($i = 1; $i < count($values); $i++) {
            $row = $values[$i];
            // Asumsi ID ada di kolom kedua (index 1)
            if (isset($row[1]) && $row[1] == $requestId) {
                $data = [
                    "email"            => $row[2]  ?? '',
                    "area_sales"       => $row[3]  ?? '',
                    "sales_code"       => $row[4]  ?? '',
                    "store_name"       => $row[5]  ?? '',
                    "spv_name"         => $row[6]  ?? '',
                    "location"         => $row[7]  ?? '',
                    "request_type"     => $row[8]  ?? '',
                    "branding_tools"   => $row[9]  ?? '',
                    "tools_size"       => $row[10] ?? '',
                    "quantity"         => $row[11] ?? '',
                    "brand"            => $row[12] ?? '',
                    "delivery"         => $row[13] ?? '',
                    "additional_notes" => $row[14] ?? ''
                ];
                $data_found = true;
                break;
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { 
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; 
      background-color: #ffffff;
    }
    .container {
      max-width: 800px; 
      margin: 250px auto;
      padding: 20px;
      background: white; 
      border-radius: 8px;
    }
    .navbar {
      background-color: #e01432;
      font-size: 1.1rem;
      padding: 14px;
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 1.8rem;
      color: #fff !important;
    }
    .navbar-nav .nav-link {
      color: white !important;
    }
    .navbar-nav .nav-link:hover {
      background-color: #e01432 !important;
      color: white !important;
      border-radius: 5px;
    }
    h1 {
      text-align: center;
    }
    input, select, textarea { 
      width: 100%; 
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc; 
      border-radius: 4px;
    }

    /* Styling khusus buat navbar button */
.navbar-toggler {
  width: 60px; /* ukuran bisa diatur sesuai selera */
  height: 50px;
  background-color: #e01432;
  border: 2px solid #e01432;
}

/* Styling khusus buat submit button */
.btn-submit {
  width: 100%;  /* atau atur ukuran sesuai keinginan */
  padding: 10px;
  background-color: rgb(255, 48, 65);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-submit:hover {
  background-color: rgb(243, 42, 42);
}

    button { 
      width: 100%;
      padding: 10px; 
      background-color: rgb(255, 48, 65);
      color: white; 
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover { 
      background-color: rgb(243, 42, 42);
    }

    
    
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="user.html">
        <img src="logo.png" alt="SOREX4 Logo" width="100" height="50" class="d-inline-block align-text-top">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="log.html">Log Aktivitas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="download_filter.html">Download Data</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">Log Out</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <h1>Form Revisi Data</h1>

    <!-- Form cari data berdasarkan Request ID -->
    <form method="POST" action="">
      <label for="request_id">Masukkan ID Request untuk Mencari Data:</label>
      <input type="text" id="request_id" name="request_id" placeholder="Masukkan ID Request" required>
      <button type="submit">Cari Data</button>
    </form>

    <?php if (isset($data['error'])): ?>
      <p style="color: red; text-align: center;"><?php echo $data['error']; ?></p>
    <?php endif; ?>

    <?php if (isset($data_found) && $data_found): ?>
      <!-- Form revisi data, termasuk file upload untuk foto -->
      <form method="POST" action="submit_revisi.php" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>

        <label for="area_sales">Area Sales:</label>
        <select id="area_sales" name="area_sales" required>
          <option value="JB">JB</option>
          <option value="JR">JR</option>
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
          <option value="spanduk_korea" <?php echo ($data['branding_tools'] == 'spanduk_korea') ? 'selected' : ''; ?>>SPANDUK KOREA</option>
          <option value="spanduk_korea_mata_ayam" <?php echo ($data['branding_tools'] == 'spanduk_korea_mata_ayam') ? 'selected' : ''; ?>>SPANDUK KOREA (PAKAI MATA AYAM)</option>
          <option value="spanduk_china" <?php echo ($data['branding_tools'] == 'spanduk_china') ? 'selected' : ''; ?>>SPANDUK CHINA</option>
          <option value="spanduk_china_mata_ayam" <?php echo ($data['branding_tools'] == 'spanduk_china_mata_ayam') ? 'selected' : ''; ?>>SPANDUK CHINA (PAKAI MATA AYAM)</option>
          <option value="poster_doff" <?php echo ($data['branding_tools'] == 'poster_doff') ? 'selected' : ''; ?>>POSTER DOFF</option>
          <option value="poster_glossy" <?php echo ($data['branding_tools'] == 'poster_glossy') ? 'selected' : ''; ?>>POSTER GLOSSY</option>
          <option value="stiker_doff" <?php echo ($data['branding_tools'] == 'stiker_doff') ? 'selected' : ''; ?>>STIKER DOFF</option>
          <option value="stiker_glossy" <?php echo ($data['branding_tools'] == 'stiker_glossy') ? 'selected' : ''; ?>>STIKER GLOSSY</option>
          <option value="stiker_oneway" <?php echo ($data['branding_tools'] == 'stiker_oneway') ? 'selected' : ''; ?>>STIKER ONEWAY</option>
          <option value="stiker_backlite" <?php echo ($data['branding_tools'] == 'stiker_backlite') ? 'selected' : ''; ?>>STIKER BACKLITE (UNTUK NEONBOX)</option>
          <option value="stiker_overprint" <?php echo ($data['branding_tools'] == 'stiker_overprint') ? 'selected' : ''; ?>>STIKER OVERPRINT (UNTUK NEONBOX)</option>
          <option value="akrilik_kapur" <?php echo ($data['branding_tools'] == 'akrilik_kapur') ? 'selected' : ''; ?>>AKRILIK KAPUR / PVC</option>
          <option value="akrilik_bening" <?php echo ($data['branding_tools'] == 'akrilik_bening') ? 'selected' : ''; ?>>AKRILIK BENING</option>
          <option value="impraboard" <?php echo ($data['branding_tools'] == 'impraboard') ? 'selected' : ''; ?>>IMPRABOARD</option>
          <option value="neonbox" <?php echo ($data['branding_tools'] == 'neonbox') ? 'selected' : ''; ?>>NEONBOX</option>
          <option value="art_paper_pop" <?php echo ($data['branding_tools'] == 'art_paper_pop') ? 'selected' : ''; ?>>ART PAPER / POP</option>
          <option value="x_banner" <?php echo ($data['branding_tools'] == 'x_banner') ? 'selected' : ''; ?>>X BANNER</option>
          <option value="roll_up_banner" <?php echo ($data['branding_tools'] == 'roll_up_banner') ? 'selected' : ''; ?>>ROLL UP BANNER</option>
          <option value="tripod_banner" <?php echo ($data['branding_tools'] == 'tripod_banner') ? 'selected' : ''; ?>>TRIPOD BANNER</option>
          <option value="stiker_lainnya" <?php echo (stripos($data['branding_tools'], 'lainnya') !== false) ? 'selected' : ''; ?>>LAINNYA</option>
        </select>
        <label for="other_tools">Lainnya (Opsional):</label>
        <textarea id="other_tools" name="other_tools" placeholder="Masukkan jenis tools lainnya..."></textarea>

        <label for="tools_size">Ukuran Tools Branding:</label>
        <input type="text" id="tools_size" name="tools_size" value="<?php echo htmlspecialchars($data['tools_size']); ?>" required>

        <label for="quantity">QTY Tools:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($data['quantity']); ?>" required>

        <label for="brand">Brand:</label>
        <select id="brand" name="brand" required>
          <option value="">-- Pilih Brand --</option>
          <option value="SOREX MAN" <?php echo ($data['brand'] == 'SOREX MAN') ? 'selected' : ''; ?>>SOREX MAN</option>
          <option value="SOREX LADIES" <?php echo ($data['brand'] == 'SOREX LADIES') ? 'selected' : ''; ?>>SOREX LADIES</option>
          <option value="SOREX KIDS" <?php echo ($data['brand'] == 'SOREX KIDS') ? 'selected' : ''; ?>>SOREX KIDS</option>
        </select>

        <label for="delivery">Pengiriman:</label>
        <input type="text" id="delivery" name="delivery" value="<?php echo htmlspecialchars($data['delivery']); ?>" required>

        <label for="additional_notes">Keterangan Tambahan:</label>
        <textarea id="additional_notes" name="additional_notes"><?php echo htmlspecialchars($data['additional_notes']); ?></textarea>

        <!-- Tambahan input file buat foto -->
        <label for="installation_photo">Foto Area Pemasangan:</label>
        <input type="file" id="installation_photo" name="installation_photo">

        <label for="design_photo">Foto Sugest Design:</label>
        <input type="file" id="design_photo" name="design_photo">

        <button type="submit" class="btn-submit">Kirim Revisi</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
