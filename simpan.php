<?php
require_once("koneksi.php");

$newRcd    = (isset($_POST['newRcd'])) ? $_POST['newRcd'] : "No";
$item_id   = isset($_POST['item_id'])     ? trim($_POST['item_id'])     : '';
$item_name = isset($_POST['item_name'])   ? trim($_POST['item_name'])   : '';
$item_cat  = isset($_POST['item_cat_id']) ? trim($_POST['item_cat_id']) : '';
$item_stok = isset($_POST['item_stock'])  ? trim($_POST['item_stock'])  : '';
$item_harga= isset($_POST['item_price'])  ? trim($_POST['item_price'])  : '';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	echo "<p>No Access";
	exit;
}

$errors = array();

// Add Validation for each field
if ($item_name == '' || $item_cat == '' || $item_stok == '' || $item_harga == '') {
	$errors[] = "Semua field wajib diisi!";
} else {
	if (!ctype_digit($item_stok)) {
		$errors[] = "Stok harus berupa angka bulat (tidak boleh minus/desimal).";
	}
	if (!is_numeric($item_harga) || $item_harga <= 0) {
		$errors[] = "Harga harus berupa angka dan lebih dari 0.";
	}
	if (strlen($item_name) > 100) {
		$errors[] = "Nama barang maksimal 100 karakter.";
	}

	// escape (anti SQL Injection)
	$n  = mysql_real_escape_string($item_name);
	$k  = mysql_real_escape_string($item_cat);
	$id = mysql_real_escape_string($item_id);

	$cekCat = mysql_query("SELECT cat_id FROM category WHERE cat_id = '$k'");
	if (mysql_num_rows($cekCat) == 0) {
		$errors[] = "Kategori tidak valid.";
	}

	$sqlDobel = "SELECT item_id FROM item WHERE item_name = '$n' AND item_cat_id = '$k'";
	if ($newRcd == "No") {
		$sqlDobel .= " AND item_id <> '$id'";
	}
	$cekDobel = mysql_query($sqlDobel);
	if (mysql_num_rows($cekDobel) > 0) {
		$errors[] = "Barang <b>" . htmlspecialchars($item_name) . "</b> sudah ada di kategori tersebut.";
	}
}

if (count($errors) > 0) {
	echo "<span class='txterror'>" . implode("<br/>", $errors) . "</span>";
	exit;
}

$stok  = (int) $item_stok;
$harga = (float) $item_harga;

$chkstate = true;

if ($newRcd == "No") {
	$sql = "UPDATE item SET
				item_name   = '$n',
				item_cat_id = '$k',
				item_stock  = $stok,
				item_price  = $harga
			WHERE item_id = '$id'";
} else {
	$id  = create_id('item');
	$sql = "INSERT INTO item (
				item_id,
				item_name,
				item_cat_id,
				item_stock,
				item_price
			) VALUES (
				'$id',
				'$n',
				'$k',
				$stok,
				$harga
			)";
}

if (!mysql_query($sql)) $chkstate = false;

$Action = ($newRcd == "No") ? "updated" : "saved";

if ($chkstate) {
	echo "<span class='txtsuccess'>Successfully $Action for <b>$id</b></span>";
} else {
	echo "<span class='txterror'>Failed $Action for <b>$id</b></span>";
	echo "<br/>";
	echo mysql_error();
}
?>