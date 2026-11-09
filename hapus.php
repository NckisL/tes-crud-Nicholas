<?php
require_once("koneksi.php");

$id = isset($_GET['id']) ? mysql_real_escape_string(trim($_GET['id'])) : '';

if ($id == '') {
    header("Location: index.php");
    exit;
}

// barang yang sudah ada transaksi tidak boleh dihapus
$cek = mysql_query("SELECT COUNT(*) AS jml FROM transactions WHERE trx_item_id = '$id'");
$r   = mysql_fetch_array($cek);

if ($r['jml'] > 0) {
    $pesan = "Barang tidak bisa dihapus karena sudah memiliki riwayat transaksi.";
} else if (mysql_query("DELETE FROM item WHERE item_id = '$id'")) {
    $pesan = "Barang berhasil dihapus.";
} else {
    $pesan = "Gagal menghapus: " . mysql_error();
}

echo "<script>
    alert(" . json_encode($pesan) . ");
    window.location = 'index.php';
</script>";
?>