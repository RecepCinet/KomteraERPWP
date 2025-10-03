<?php

$sku = $_GET['sku'];
$stmt = $conn->prepare("select tur T,cozum C,lisansSuresi L,listeFiyati F,listeFiyatiUpLift UF from aa_erp_kt_fiyat_listesi fl where fl.SKU=:sku");
$stmt->execute(['sku' => $sku]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($gelen[0]);
echo $json;
?>
