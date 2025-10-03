<?php

$fn = $_GET['firsat_no'];
$stmt = $conn->prepare("select onay1_oran,onay1_mail,onay2_oran,onay2_mail from aa_erp_kt_ayarlar_onaylar_kar where bayi_ch_kodu=:ch_kodu and marka=:marka and cozum=:cozum");
$stmt->execute(['ch_kodu' => $ch_kodu]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
//$json= json_encode($gelen[0]);
echo $gelen['val'];

?>

