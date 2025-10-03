<?php
error_reporting(0);
ini_set("display_errors", false);

//http://172.16.80.214/_engines/acil_onay&param=MTIyMTcwNA==

$param= "T" . (int)base64_decode($_GET['param'])/4;
$kim=$_GET['user'];
$sira=$_GET['sira'];
$teklif_no=$_GET['teklif_no'];

$ek1=" and ONAY1_KIM='$kim'";
$ek2=" and ONAY2_KIM='$kim'";


if ($teklif_no!="") {
    $param=$teklif_no;
    $ek1="";
    $ek2="";
}

if ($sira==="" || !$sira) {
    $string="update aa_erp_kt_teklifler set ONAY1='1',ONAY1_KIM='' where TEKLIF_NO=:teklif_no$ek1";
    
    $url="http://172.16.84.214/_engines/tekil_getir.php?cmd=onaylar_mailler&onay2=1&teklif_no=$param";
    $cevap=file_get_contents($url);
    echo $cevap;
    
    $url="http://172.16.84.214/_engines/onaylandimi.php?tip=1&kim=1&teklif_no=$param";
    $cevap=file_get_contents($url);
    echo $cevap;
}
if ($sira==="2") {
    $string="update aa_erp_kt_teklifler set ONAY2='1',ONAY2_KIM='' where TEKLIF_NO=:teklif_no$ek2";
    $url="http://172.16.84.214/_engines/onaylandimi.php?tip=1&kim=2&teklif_no=$param";
    $cevap=file_get_contents($url);
    echo $cevap;
}

include '../_conn.php';

echo "OK";

$stmt = $conn->prepare($string);
$stmt->execute(['teklif_no'=>$param]);

