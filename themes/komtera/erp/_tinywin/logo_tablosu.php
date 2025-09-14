<?php

error_reporting(0);
ini_set("display_errors", false);

include '../../_conn.php';

$siparis_no = $_GET['siparis_no'];

$stmt = $conn->prepare("select * from ARYD_FIS_AKTARIM where [NO]=:siparis_no");
$stmt->execute(['siparis_no' => $siparis_no]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);


$baslik = "<table width='100%' border=1><tr>\n";
$icerik = "";
$kac = count($rs);
$say = 0;
while ($say<count($rs)) {
    $say ++;
    $satir = "<tr>\n";
    for ($i = 1; $i <= odbc_num_fields($rs); $i ++) {
        if ($say == 1) {
            $baslik .= "<td class='baslik' align=center><b>" . odbc_field_name($rs, $i) . "</b></td>\n";
        }
        $b = (odbc_result($rs, odbc_field_name($rs, $i)));
//        if (is_numeric($b)) {
//            $b = number_format($b, 0, ",", ".");
//        }
        $satir .= "<td>" . $param=iconv("ISO-8859-9", "UTF-8", $b) . "</td>\n";
    }

    $satir .= "</tr>\n";
    $icerik .= $satir;
}
$baslik .= "</tr>\n";
$icerik .= "</table>\n";
echo $baslik;
echo $icerik;

odbc_close($conn);

?>
<br /><br />
SONUC: 0 Aktarim motoru henuz calismadi, SONUC: 4 ise ARYD'nin motoru calisip siparisi logoya aktarmistir.<br />
DOVIZ_KUR: 0 TL, 1 DOLAR, 2 EURO icin kullaniliyor.<br />
