<?php
require_once("koneksi.php");

$item_list = mysql_query("SELECT item_id, item_name, item_stock FROM item ORDER BY item_name");
$opt = "<option value=\"\">-- Pilih Barang --</option>";
while ($k = mysql_fetch_array($item_list)) {
    $opt .= "<option value=\"" . $k['item_id'] . "\">" . htmlspecialchars($k['item_name']) . " (stok: " . $k['item_stock'] . ")</option>";
}

$today = date('Y-m-d');

$dataJS = "
    $(document).ready(function () {
        $('#frm_grp').validate({
            messages: {
                trx_amount: { digits: 'Jumlah harus angka bulat', min: 'Jumlah minimal 1' }
            },
            invalidHandler: function(event, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    alert('You missed ' + errors + ' fields. They have been highlighted.');
                }
            },
            submitHandler : function(form){
                $(form).ajaxSubmit({
                    url : 'transaksi_simpan.php',
                    type : 'post',
                    beforeSubmit: function(){
                        $('#btn_save').hide();
                    },
                    success: function (responseText, statusText) {
                        $('#btn_save').show();
                        $('#useradm_users_rst').html(responseText);
                        if (responseText.indexOf('txtsuccess') > -1) {
                            setTimeout(function () {
                                window.location = 'transaksi.php';
                            }, 1000);
                        }
                    }
                });
            }
        });
    });

    function Simpan() {
        $('#frm_grp').submit();
    }
";

echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>Transaksi Baru</title>
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
        input[type=date],
        select {
            padding: 6px;
            width: 280px;
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
        .btn-simpan {
            background: #27ae60;
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
    <h2>Transaksi Baru</h2>
    <div class=\"box\">
        <form id=\"frm_grp\">
            <table>
                <tr>
                    <td class=\"Kolom_Text\"><b>Barang</b></td>
                    <td><select id=\"trx_item_id\" name=\"trx_item_id\" class=\"required\">$opt</select></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Jenis</b></td>
                    <td>
                        <select id=\"trx_status\" name=\"trx_status\" class=\"required\">
                            <option value=\"Y\">Barang Masuk</option>
                            <option value=\"N\">Barang Keluar</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Jumlah</b></td>
                    <td><input type=\"text\" id=\"trx_amount\" name=\"trx_amount\" class=\"required digits\" min=\"1\"></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Tanggal</b></td>
                    <td><input type=\"date\" id=\"trx_date\" name=\"trx_date\" class=\"required\" value=\"$today\"></td>
                </tr>
                <tr>
                    <td class=\"Kolom_Text\"><b>Keterangan</b></td>
                    <td><input type=\"text\" id=\"trx_desc\" name=\"trx_desc\" maxlength=\"200\"></td>
                </tr>
                <tr>
                    <td></td>
                    <td style=\"padding-top:10px;\">
                        <a href=\"transaksi.php\" class=\"btn btn-batal\">Cancel</a>
                        <a href=\"#\" id=\"btn_save\" class=\"btn btn-simpan\" onclick=\"Simpan(); return false;\">Save</a>
                    </td>
                </tr>
            </table>
        </form>
        <div id=\"useradm_users_rst\"></div>
    </div>
</body>
</html>";
?>