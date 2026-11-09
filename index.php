<?php
include("koneksi.php");

$where = "";
$cari ="";
$sql = "SELECT
            i.item_id,
            i.item_name,
            c.cat_name,
            i.item_stock,
            i.item_price
        FROM item i
        JOIN category c ON i.item_cat_id = c.cat_id
        /*$where*/
        ORDER BY i.item_id DESC
        ";

$res = mysql_query($sql) or die("Query error: " . mysql_error());
$row = mysql_num_rows($res);

$DATATABLE = "
<table class=\"tabel\" id=\"table_list\">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Harga</th>
        </tr>
    </thead>
    <tbody>";

if ($row == 0) {
    $DATATABLE .= "<tr><td colspan=\"6\" align=\"center\">Data tidak ditemukan</td></tr>";
}

for ($r = 0; $r < $row; $r++) {
    $data       = mysql_fetch_array($res);

    $item_id    = $data['item_id'];
    $item_name  = htmlspecialchars($data['item_name']);
    $item_cat   = htmlspecialchars($data['cat_name']);
    $item_stock = $data['item_stock'];
    $item_price = number_format($data['item_price'], 0, ',', '.');
    $no         = $r + 1;

    $DATATABLE .= "
        <tr class=\"baris\" data-id=\"$item_id\" data-name=\"$item_name\">
            <td>$no</td>
            <td>$item_id</td>
            <td>$item_name</td>
            <td>$item_cat</td>
            <td>$item_stock</td>
            <td>Rp $item_price</td>
        </tr>";
}

$DATATABLE .= "
    </tbody>
</table>";

$cariVal = htmlspecialchars($cari);

echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>Inventaris Gudang</title>
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
        .baris {
            cursor: pointer;
        }
        .baris:hover td {
            background: #f1f7ff;
        }
        .row_selected td {
            background: #ffe9a8 !important;
        }
        .btn {
            display: inline-block;
            padding: 7px 14px;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-tambah {
            background: #27ae60;
        }
        .btn-edit {
            background: #f39c12;
        }
        .btn-hapus {
            background: #e74c3c;
        }
        .bar {
            margin-bottom: 15px;
        }
        .bar input[type=text] {
            padding: 6px;
            width: 220px;
        }
        .bar button {
            padding: 6px 12px;
        }
        .bar .kanan { 
            float: right; 
        }
    </style>
</head>
<body>
    <h2>Inventaris Barang Gudang</h2>

    <div class=\"bar\">
        <a href=\"tambah.php\" class=\"btn btn-tambah\">+ New</a>
        <a href=\"#\" class=\"btn btn-edit\" onclick=\"EditItem(); return false;\">Edit</a>
        <a href=\"#\" class=\"btn btn-hapus\" onclick=\"DeleteItem(); return false;\">Delete</a>

        <form method=\"get\" class=\"kanan\">
            <input type=\"text\" name=\"cari\" placeholder=\"Cari barang / kategori\" value=\"$cariVal\">
            <button type=\"submit\">Cari</button>
        </form>
    </div>

    $DATATABLE
    <script>
        var selectedId   = '';
        var selectedName = '';

        // klik baris -> tandai sebagai terpilih
        var rows = document.querySelectorAll('#table_list tbody tr.baris');
        for (var i = 0; i < rows.length; i++) {
            rows[i].onclick = function () {
                for (var j = 0; j < rows.length; j++) {
                    rows[j].classList.remove('row_selected');
                }
                this.classList.add('row_selected');
                selectedId   = this.getAttribute('data-id');
                selectedName = this.getAttribute('data-name');
            };
        }

        function EditItem() {
            if (selectedId == '') {
                alert('No Row Selected!');
                return;
            }
            window.location = 'edit.php?id=' + encodeURIComponent(selectedId);
        }

        function DeleteItem() {
            if (selectedId == '') {
                alert('No Row Selected!');
                return;
            }
            if (confirm('Yakin hapus ' + selectedName + '?')) {
                window.location = 'hapus.php?id=' + encodeURIComponent(selectedId);
            }
        }
    </script>
</body>
</html>";

?>