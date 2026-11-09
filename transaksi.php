<?php
require_once("koneksi.php");

// Filter tanggal
function is_valid_date($date) {
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) return false;
    return checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
}

$date_from = isset($_GET['dari'])   ? trim($_GET['dari'])   : '';
$date_to   = isset($_GET['sampai']) ? trim($_GET['sampai']) : '';
$msg       = "";
$where     = "";

if ($date_from != '' && !is_valid_date($date_from)) {
    $msg = "Format tanggal 'dari' tidak valid.";
    $date_from = '';
}
if ($date_to != '' && !is_valid_date($date_to)) {
    $msg = "Format tanggal 'sampai' tidak valid.";
    $date_to = '';
}
if ($date_from != '' && $date_to != '' && $date_from > $date_to) {
    $msg = "Tanggal 'dari' tidak boleh lebih besar dari 'sampai'.";
    $date_from = '';
    $date_to = '';
}

$cond = array();
if ($date_from != '') $cond[] = "t.trx_date >= '" . mysql_real_escape_string($date_from) . "'";
if ($date_to != '')   $cond[] = "t.trx_date <= '" . mysql_real_escape_string($date_to) . "'";
if (count($cond) > 0) $where = "WHERE " . implode(" AND ", $cond);

$sql = "SELECT
            t.trx_id,
            t.trx_date,
            i.item_name,
            t.trx_status,
            t.trx_amount,
            i.item_price,
            t.trx_desc
        FROM transactions t
        JOIN item i ON t.trx_item_id = i.item_id
        $where
        ORDER BY t.trx_date DESC, t.trx_id DESC";

$res = mysql_query($sql) or die("Query error: " . mysql_error());
$row = mysql_num_rows($res);

$total_in_qty    = 0;
$total_out_qty   = 0;
$total_in_value  = 0;
$total_out_value = 0;

$DATATABLE = "
<table class=\"tabel\" id=\"table_list\">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Transaksi</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Nilai</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>";

if ($row == 0) {
    $DATATABLE .= "<tr><td colspan=\"8\" align=\"center\">Data tidak ditemukan</td></tr>";
}

for ($r = 0; $r < $row; $r++) {
    $data = mysql_fetch_array($res);

    $trx_id    = $data['trx_id'];
    $trx_date  = $data['trx_date'];
    $item_name = htmlspecialchars($data['item_name']);
    $qty       = (int) $data['trx_amount'];
    $value     = $qty * $data['item_price'];
    $desc      = htmlspecialchars($data['trx_desc']);
    $no        = $r + 1;

    if ($data['trx_status'] == 'Y') {
        $type_label = "<span class=\"masuk\">Masuk</span>";
        $total_in_qty   += $qty;
        $total_in_value += $value;
    } else {
        $type_label = "<span class=\"keluar\">Keluar</span>";
        $total_out_qty   += $qty;
        $total_out_value += $value;
    }

    $value_fmt = number_format($value, 0, ',', '.');

    $DATATABLE .= "
        <tr>
            <td>$no</td>
            <td>$trx_id</td>
            <td>$trx_date</td>
            <td>$item_name</td>
            <td>$type_label</td>
            <td>$qty</td>
            <td>Rp $value_fmt</td>
            <td>$desc</td>
        </tr>";
}

$DATATABLE .= "
    </tbody>
</table>";

$REKAP = "
<div class=\"rekap\">
    <b>Rekap</b>
    <table>
        <tr><td>Jumlah transaksi</td><td>: $row</td></tr>
        <tr><td>Total barang masuk</td><td>: $total_in_qty unit (Rp " . number_format($total_in_value, 0, ',', '.') . ")</td></tr>
        <tr><td>Total barang keluar</td><td>: $total_out_qty unit (Rp " . number_format($total_out_value, 0, ',', '.') . ")</td></tr>
    </table>
</div>";

$val_from = htmlspecialchars($date_from);
$val_to   = htmlspecialchars($date_to);
$alert    = ($msg != '') ? "<div class=\"txterror\">" . htmlspecialchars($msg) . "</div>" : "";

echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>Transaksi Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f8;
        }
        h2 {
            margin-bottom: 15px;
        }
        .tabel {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .tabel th,
        .tabel td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 14px;
        }
        .tabel th {
            background: #2c3e50;
            color: #fff;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 7px 14px;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            font-size: 13px;
            cursor: pointer;
            border: 0;
        }
        .btn-tambah {
            background: #27ae60;
        }
        .btn-kembali {
            background: #7f8c8d;
        }
        .btn-filter {
            background: #2980b9;
        }
        .bar {
            margin-bottom: 15px;
        }
        .bar .kanan {
            float: right;
        }
        .bar input[type=date] {
            padding: 5px;
        }
        .masuk {
            color: #27ae60;
            font-weight: bold;
        }
        .keluar {
            color: #c0392b;
            font-weight: bold;
        }
        .txterror {
            color: #c0392b;
            margin-bottom: 10px;
        }
        .rekap {
            margin-top: 20px;
            background: #fff;
            border: 1px solid #ddd;
            padding: 12px 16px;
            width: 420px;
            font-size: 14px;
        }
        .rekap td {
            padding: 3px 6px;
        }

    </style>
</head>
<body>
    <h2>Transaksi Barang Masuk / Keluar</h2>

    <div class=\"bar\">
        <a href=\"index.php\" class=\"btn btn-kembali\">&laquo; Barang</a>
        <a href=\"transaksi_tambah.php\" class=\"btn btn-tambah\">+ Transaksi Baru</a>

        <form method=\"get\" class=\"kanan\" onsubmit=\"return cekTanggal();\">
            Dari <input type=\"date\" id=\"dari\" name=\"dari\" value=\"$val_from\">
            Sampai <input type=\"date\" id=\"sampai\" name=\"sampai\" value=\"$val_to\">
            <button type=\"submit\" class=\"btn btn-filter\">Filter</button>
            <a href=\"transaksi.php\">Reset</a>
        </form>
    </div>

    $alert
    $DATATABLE
    $REKAP

    <script>
        function cekTanggal() {
            var d = document.getElementById('dari').value;
            var s = document.getElementById('sampai').value;
            if (d != '' && s != '' && d > s) {
                alert('Tanggal dari tidak boleh lebih besar dari sampai!');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>";
?>