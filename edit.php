<?php
require_once("koneksi.php");

$id = isset($_GET['id']) ? mysql_real_escape_string(trim($_GET['id'])) : '';

$res = mysql_query("SELECT 
                        item_id,
                        item_name, 
                        item_cat_id, 
                        item_stock, 
                        item_price
                    FROM item 
                    WHERE 
                        item_id = '$id'
                    ");
if (!$res || mysql_num_rows($res) == 0) {
    die("<p>Data tidak ditemukan. <a href=\"index.php\">Kembali</a></p>");
}
$data = mysql_fetch_array($res);

$item_id    = $data['item_id'];
$item_name  = htmlspecialchars($data['item_name']);
$item_cat   = $data['item_cat_id'];
$item_stock = $data['item_stock'];
$item_price = $data['item_price'] + 0;

$cat_list = mysql_query("SELECT 
                            cat_id, 
                            cat_name 
                        FROM category 
                        ORDER BY cat_name
                        ");
$opt = "<option value=\"\">-- Pilih Kategori --</option>";
while ($k = mysql_fetch_array($cat_list)) {
    $sel  = ($k['cat_id'] == $item_cat) ? " selected" : "";
    $opt .= "<option value=\"" . $k['cat_id'] . "\"$sel>" . htmlspecialchars($k['cat_name']) . "</option>";
}

$dataJS = "
    $(document).ready(function () {
        $('#frm_grp').validate({
            messages: {
                item_stock: { digits: 'Stok harus angka bulat' },
                item_price: { number: 'Harga harus angka', min: 'Harga harus lebih dari 0' }
            },
            invalidHandler: function(event, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    alert('You missed ' + errors + ' fields. They have been highlighted.');
                }
            },
            submitHandler : function(form){
                $(form).ajaxSubmit({
                    url : 'simpan.php',
                    type : 'post',
                    beforeSubmit : function(){
                        $('#btn_udt').hide();
                    },
                    success: function (responseText, statusText) {
                        $('#btn_udt').show();
                        $('#useradm_users_rst').html(responseText);
                        if (responseText.indexOf('txtsuccess') > -1) {
                            setTimeout(function () {
                                window.location = 'index.php';
                            }, 1000);
                        }
                    }
                });
            }
        });
    });

    function Perbarui() {
        $('#frm_grp').submit();
    }
";

$output = "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>Edit Barang</title>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js\"></script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js\"></script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js\"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f8;
        }
        .box {
            background: #fff;
            padding: 20px;
            width: 520px;
            border: 1px solid #ddd;
        }
        .Kolom_Text {
            width: 130px;
            padding: 8px 0;
            vertical-align: top;
        }
        input[type=text],
        select {
            padding: 6px;
            width: 280px;
        }
        input[readonly] {
            background: #eee;
        }
        input.error,
        select.error {
            border: 1px solid #e74c3c;
            background: #fdecea;
        }
        label.error {
            display: block;
            color: #c0392b;
            font-size: 12px;
        }
        #useradm_users_rst {
            margin-top: 15px;
            font-size: 14px;
        }
        .txtsuccess {
            color: #27ae60;
            font-weight: bold;
        }
        .txterror {
            color: #c0392b;
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
        .btn-update {
            background: #f39c12;
        }
        .btn-batal {
            background: #7f8c8d;
        }
    </style>
    <script>
        $dataJS
    </script>
</head>
<body>
    <h2>Edit Barang</h2>
    <div class=\"box\">
        <form id=\"frm_grp\">
            <input type=\"hidden\" id=\"newRcd\" name=\"newRcd\" value=\"No\">
            <table>
                <tr>
                    <td class=\"Kolom_Text\"><b>Kode Barang</b></td>
                    <td><input type=\"text\" id=\"item_id\" name=\"item_id\" readonly value=\"$item_id\"></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Nama Barang</b></td>
                    <td><input type=\"text\" id=\"item_name\" name=\"item_name\" class=\"required\" maxlength=\"100\" value=\"$item_name\"></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Kategori</b></td>
                    <td><select id=\"item_cat_id\" name=\"item_cat_id\" class=\"required\">$opt</select></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Stok</b></td>
                    <td><input type=\"text\" id=\"item_stock\" name=\"item_stock\" class=\"required digits\" value=\"$item_stock\"></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Harga (Rp)</b></td>
                    <td><input type=\"text\" id=\"item_price\" name=\"item_price\" class=\"required number\" min=\"1\" value=\"$item_price\"></td>
                </tr>
                <tr>
                    <td></td>
                    <td style=\"padding-top:10px;\">
                        <a href=\"index.php\" class=\"btn btn-batal\">Cancel</a>
                        <a href=\"#\" id=\"btn_udt\" class=\"btn btn-update\" onclick=\"Perbarui(); return false;\">Update</a>
                    </td>
                </tr>
            </table>
        </form>
        <div id=\"useradm_users_rst\"></div>
    </div>
</body>
</html>";

echo $output;
?>