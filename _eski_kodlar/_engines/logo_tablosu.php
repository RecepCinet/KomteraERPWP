<?php
error_reporting(0);
ini_set("display_errors", false);

include '../_conn.php';
$siparis_no = $_GET['siparis_no'];
$url = "select [NO],SONUC,MESAJ,* from ARYD_FIS_AKTARIM where [NO]='$siparis_no'";
//echo $url;
$stmt = $conn->prepare($url);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($rs);
$satir = "<table width=8000 border=1>\n";
$say = 0;
foreach ($rs as $record) {
    if ($say === 0) {
        $satir .= "<tr>\n";
        $sutun = "";
        foreach ($record as $key => $value) {
            $sutun .= "<td><b>" . $key . "</b></td>";
        }
        $satir .= $sutun;
        $satir .= "</tr>\n";
    }
    $say++;
    $satir .= "<tr>\n";
    $sutun = "";
    foreach ($record as $key => $value) {
        $sutun .= "<td>" . $value . "</td>";
    }
    $satir .= $sutun;
    $satir .= "</tr>\n";
}
$satir .= "</table>";

echo $satir;
odbc_close($conn);
?>
<br />
SONUC: 0 Aktarim motoru henuz calismadi, SONUC: 4 ise ARYD'nin motoru calisip siparisi logoya aktarmistir.<br />
DOVIZ_KUR: 0 TL, 1 DOLAR, 2 EURO icin kullaniliyor.<br />
