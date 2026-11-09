<?php
require_once("koneksi.php");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<p>No Access";
    exit;
}

$trx_item   = isset($_POST['trx_item_id']) ? trim($_POST['trx_item_id']) : '';
$trx_status = isset($_POST['trx_status'])  ? trim($_POST['trx_status'])  : '';
$trx_amount = isset($_POST['trx_amount'])  ? trim($_POST['trx_amount'])  : '';
$trx_date   = isset($_POST['trx_date'])    ? trim($_POST['trx_date'])    : '';
$trx_desc   = isset($_POST['trx_desc'])    ? trim($_POST['trx_desc'])    : '';

$errors = array();

if ($trx_item == '' || $trx_status == '' || $trx_amount == '' || $trx_date == '') {
    $errors[] = "Barang, jenis, jumlah, dan tanggal wajib diisi!";
} else {
    if (!in_array($trx_status, array('Y', 'N'))) {
        $errors[] = "Jenis transaksi tidak valid.";
    }
    if (!ctype_digit($trx_amount) || (int) $trx_amount <= 0) {
        $errors[] = "Jumlah harus angka bulat lebih dari 0.";
    }

    $date_ok = false;
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $trx_date, $m)) {
        $date_ok = checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }
    if (!$date_ok) {
        $errors[] = "Format tanggal tidak valid (YYYY-MM-DD).";
    }
    if (strlen($trx_desc) > 200) {
        $errors[] = "Keterangan maksimal 200 karakter.";
    }

    $i = mysql_real_escape_string($trx_item);
    $check = mysql_query("SELECT item_stock FROM item WHERE item_id = '$i'");
    if (mysql_num_rows($check) == 0) {
        $errors[] = "Barang tidak ditemukan.";
    } else if (count($errors) == 0) {
        $item = mysql_fetch_array($check);
        if ($trx_status == 'N' && (int) $trx_amount > (int) $item['item_stock']) {
            $errors[] = "Stok tidak cukup! Stok saat ini: " . $item['item_stock'];
        }
    }
}

if (count($errors) > 0) {
    echo "<span class='txterror'>" . implode("<br/>", $errors) . "</span>";
    exit;
}

$qty = (int) $trx_amount;
$d   = mysql_real_escape_string($trx_desc);
$t   = mysql_real_escape_string($trx_date);
$id  = create_id('transactions');

// update stock + record
mysql_query("START TRANSACTION");

if ($trx_status == 'Y') {
    $ok1 = mysql_query("UPDATE item 
                        SET item_stock = item_stock + $qty 
                        WHERE item_id = '$i'");
} else {
    $ok1 = mysql_query("UPDATE item 
                        SET item_stock = item_stock - $qty 
                        WHERE item_id = '$i' 
                        AND item_stock >= $qty");
    if ($ok1 && mysql_affected_rows() == 0) $ok1 = false;
}

$ok2 = mysql_query("INSERT INTO transactions (
                        trx_id, 
                        trx_item_id, 
                        trx_status, 
                        trx_amount, 
                        trx_date, 
                        trx_desc
                    )
                    VALUES (
                        '$id', 
                        '$i', 
                        '$trx_status', 
                        $qty, 
                        '$t', 
                        '$d'
                    )");

if ($ok1 && $ok2) {
    mysql_query("COMMIT");
    echo "<span class='txtsuccess'>Successfully saved for <b>$id</b></span>";
} else {
    mysql_query("ROLLBACK");
    echo "<span class='txterror'>Failed saved for <b>$id</b></span><br/>" . mysql_error();
}
?>