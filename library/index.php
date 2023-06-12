<?php
// Mendefinisikan daftar bandara dan tarif pajak
$bandaraAsal = [
    'Soekarno-Hatta (CGK)' => 50000,
    'Husein Sastranegara (BDO)' => 50000,
    'Abdul Rachman Saleh (MLG)' => 40000,
    'Juanda (SUB)' => 40000
];

$bandaraTujuan = [
    'Ngurah Rai (DPS)' => 80000,
    'Hasanuddin (UPG)' => 70000,
    'Inanwatan (INX)' => 90000,
    'Sultan Iskandarmuda (BTJ)' => 70000
];

// Membaca data JSON dari file database
$jsonString = file_get_contents('../data/data.json');
$database = json_decode($jsonString, true);

// Jika file database kosong, inisialisasi dengan array kosong
if ($database === null) {
    $database = [];
}

// Menangani form input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maskapai = $_POST['maskapai'];
    $asal = $_POST['asal'];
    $tujuan = $_POST['tujuan'];
    $harga = $_POST['harga']; // Menambahkan input harga tiket

    // Memastikan bandara asal dan tujuan valid
    if (!array_key_exists($asal, $bandaraAsal) || !array_key_exists($tujuan, $bandaraTujuan)) {
        die('Bandara asal atau tujuan tidak valid');
    }

    // proses menghitung
    $hargaTiket = $harga;
    $pajak = $bandaraAsal[$asal] + $bandaraTujuan[$tujuan];
    $totalHarga = $hargaTiket + $pajak;

    // Menambahkan data ke database
    $database[] = [
        'maskapai' => $maskapai,
        'asal' => $asal,
        'tujuan' => $tujuan,
        'harga_tiket' => $hargaTiket,
        'pajak' => $pajak,
        'total_harga' => $totalHarga
    ];

    // mengurutkan data berdasarkan urutan abjad
    usort($database, function($a, $b) {
        return strcmp($a['maskapai'], $b['maskapai']);
    });

    // Menyimpan data ke file database
    $jsonData = json_encode($database, JSON_PRETTY_PRINT);
    file_put_contents('../data/data.json', $jsonData);

    // Redirect kembali ke halaman utama
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Rute Penerbangan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url(../img/gambar.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            margin-top: 100px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Form Input Rute Penerbangan</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="maskapai">Maskapai</label>
                <input type="text" class="form-control" id="maskapai" name="maskapai" required>
            </div>
            <div class="form-group">
                <label for="asal">Bandara Asal</label>
                <select class="form-control" id="asal" name="asal" required>
                    <?php foreach ($bandaraAsal as $bandara => $tarifPajak) : ?>
                        <option value="<?php echo $bandara; ?>"><?php echo $bandara; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tujuan">Bandara Tujuan</label>
                <select class="form-control" id="tujuan" name="tujuan" required>
                    <?php foreach ($bandaraTujuan as $bandara => $tarifPajak) : ?>
                        <option value="<?php echo $bandara; ?>"><?php echo $bandara; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="harga">Harga Tiket</label>
                <input type="number" class="form-control" id="harga" name="harga" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <h2>Data Rute Penerbangan</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Maskapai</th>
                    <th>Asal Penerbangan</th>
                    <th>Tujuan Penerbangan</th>
                    <th>Harga Tiket</th>
                    <th>Pajak</th>
                    <th>Total Harga Tiket</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($database as $data) : ?>
                    <tr>
                        <td><?php echo $data['maskapai']; ?></td>
                        <td><?php echo $data['asal']; ?></td>
                        <td><?php echo $data['tujuan']; ?></td>
                        <td><?php echo $data['harga_tiket']; ?></td>
                        <td><?php echo $data['pajak']; ?></td>
                        <td><?php echo $data['total_harga']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
