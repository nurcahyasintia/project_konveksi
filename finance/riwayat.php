<?php
include "koneksi.php";

$pemasukan = mysqli_query(
    $koneksi,
    "SELECT tanggal, kategori, sumber AS pihak, nominal, referensi, keterangan, 'Pemasukan' AS jenis
     FROM income_transactions"
);

$pengeluaran = mysqli_query(
    $koneksi,
    "SELECT tanggal, kategori, tujuan AS pihak, nominal, referensi, keterangan, 'Pengeluaran' AS jenis
     FROM expense_transactions"
);

$transaksi = [];

while ($row = mysqli_fetch_assoc($pemasukan)) {
    $transaksi[] = $row;
}

while ($row = mysqli_fetch_assoc($pengeluaran)) {
    $transaksi[] = $row;
}

usort($transaksi, function ($a, $b) {
    return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fde8f0;
            margin: 0;
            padding: 30px;
        }

        h1 {
            color: #ec1970;
        }

        .table-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #ec1970;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <h1>Riwayat Transaksi</h1>

    <p>Daftar seluruh pemasukan dan pengeluaran perusahaan.</p>

    <div class="table-box">

        <table>

            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Sumber / Tujuan</th>
                <th>Nominal</th>
                <th>Referensi</th>
                <th>Keterangan</th>
            </tr>

            <?php foreach ($transaksi as $row) { ?>

                <tr>
                    <td><?= $row['tanggal']; ?></td>
                    <td><?= $row['jenis']; ?></td>
                    <td><?= $row['kategori']; ?></td>
                    <td><?= $row['pihak']; ?></td>
                    <td>Rp <?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                    <td><?= $row['referensi']; ?></td>
                    <td><?= $row['keterangan']; ?></td>
                </tr>

            <?php } ?>

        </table>

    </div>

</body>

</html>