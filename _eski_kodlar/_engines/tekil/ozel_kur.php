<?php

$k = $_GET['kur'];
$s = $_GET['siparis'];

$stmt = $conn->prepare("update aa_erp_kt_siparisler set OZEL_KUR=:k where SIPARIS_NO=:sn");
$stmt->execute(['k' => $k,'sn' => $s]);

?>
