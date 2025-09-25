<?php

error_reporting(0);
ini_set("display_errors", false);

include '../../_conn.php';

$bb=$_GET['bb'];

$stmt = $conn->prepare("select KILIT from aa_erp_kt_teklifler where TEKLIF_NO=(select X_TEKLIF_NO from aa_erp_kt_teklifler_urunler u WHERE id=:id)");
$stmt->execute(['id' => $bb]); 
$gelen = $stmt->fetch()["KILIT"][0];

if ($gelen==="1") {
    die(__('Hata', 'komtera') . "," . __('KiLiT', 'komtera'));
}

$sql = "delete from aa_erp_kt_teklifler_urunler WHERE id=?";
$stmt= $conn->prepare($sql);
$stmt->execute([$bb]);

?>
