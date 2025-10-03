<?php

$i = $_GET['i'];
$i2 = $_GET['i2'];
$sn = $_GET['sn'];

$stmt = $conn->prepare("update aa_erp_kt_siparisler set SIPARIS_DURUM=:i,SIPARIS_DURUM_ALT=:i2 where SIPARIS_NO=:sn");
$stmt->execute(['i' => $i,'i2' => $i2, 'sn' => $sn]);

?>
