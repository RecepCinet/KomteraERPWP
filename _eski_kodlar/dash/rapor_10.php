<?php
header('Content-Type: text/html; charset=utf-8');
$conn2 = odbc_connect('KomteraERP', 'Recep Cinet', 'KlyA2gw1');
setlocale (LC_ALL, "");
$sqlstring = "SELECT * FROM T_95_USERS where group_latin='satis'";
$sql = odbc_exec($conn2, $sqlstring);
while ($rs = odbc_fetch_row($sql)) {
    $en = "http://172.16.80.214:8080/RAPI/satis_hedef.php?r=1&logo=" . str_replace(" ","_",odbc_result($rs,'kullanici_latin'));
    echo odbc_result($rs,'kullanici_latin') . "<br />";
    //$cevap= file_get_contents($en);
    //echo $cevap;
}
odbc_close_all();

?>
