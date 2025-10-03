<?php
include '_func.php';
include '../_conn.php';
$siparis_no=$_GET['siparis_no'];
$fatura=$_GET['fatura'];

$hangisi="";
if ($fatura=="1") {
    // Fatura
    $sql = "select LOGICALREF,FICHENO from LG_319_01_INVOICE where DOCODE='$siparis_no'";
    $hangisi="_status_f='1'";
} else {
    // Irsaliye
    $sql = "select LOGICALREF,FICHENO from LG_319_01_STFICHE where DOCODE='$siparis_no'";
    $hangisi="_status_i='1'";
}
echo $sql . "\n";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($data)>0) {
    $LOGID=$data[0]['LOGICALREF'];
    $FISNO=$data[0]['FICHENO'];
    $sql="update aa_erp_kt_fatura_i set $hangisi,r_result='success',r_response='Bilinmeyen', r_FisNo='$FISNO',r_LogoId='$LOGID' where siparisNo='$siparis_no'";
    echo $sql . "\n";
    $up_st = $conn->prepare($sql);
    $up_st->execute();
} else {
    // Bulunamadi!
}

?> 