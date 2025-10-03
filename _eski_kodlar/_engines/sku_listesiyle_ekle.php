<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

include '../_conn.php';

$skular=$_GET['skular'];
$teklif=$_GET['teklif_id'];

$stmt = $conn->prepare("select KILIT from aa_erp_kt_teklifler where TEKLIF_NO=(select X_TEKLIF_NO from aa_erp_kt_teklifler_urunler u WHERE id=:id)");
$stmt->execute(['id' => $bb]); 
$gelen = $stmt->fetch()["KILIT"][0];

if ($gelen==="1") {
    die();
}

$sql = "delete from aa_erp_kt_teklifler_urunler WHERE id=?";
$stmt= $conn->prepare($sql);
$stmt->execute([$bb]);


?>
