<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$db     = "inventory";

$conn = mysql_connect($host, $user, $pass);
if (!$conn) {
    die("Connection failed: " . mysql_error());
}

mysql_select_db($db, $conn) or die("Database not found: " . mysql_error());

//function buat id unik berdasarkan tabel
function create_id($tabel) {
    $prefix = array(
        'category'     => array('cat_id',  'CAT-'),
        'item'         => array('item_id', 'ITM-'),
        'transactions' => array('trx_id',  'TRX-')
    );

    if (!isset($prefix[$tabel])) {
        die("Tabel tidak dikenal: " . $tabel);
    }

    $kolom = $prefix[$tabel][0];
    $awal  = $prefix[$tabel][1] . date('ymd');   

    $q = mysql_query("SELECT MAX($kolom) AS maks FROM $tabel WHERE $kolom LIKE '" . $awal . "%'");
    $r = mysql_fetch_array($q);

    $urut = $r['maks'] ? ((int) substr($r['maks'], -3) + 1) : 1;
    return $awal . str_pad($urut, 3, '0', STR_PAD_LEFT);
}
?>