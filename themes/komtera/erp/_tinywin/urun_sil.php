<?php

error_reporting(0);
ini_set("display_errors", false);

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$bb=$_GET['bb'];

$tekliflerTable = getTableName('aa_erp_kt_teklifler');
$urunlerTable = getTableName('aa_erp_kt_teklifler_urunler');

$stmt = $conn->prepare("select KILIT from {$tekliflerTable} where TEKLIF_NO=(select X_TEKLIF_NO from {$urunlerTable} u WHERE id=:id)");
$stmt->execute(['id' => $bb]); 
$gelen = $stmt->fetch()["KILIT"][0];

if ($gelen==="1") {
    die(__('Hata', 'komtera') . "," . __('KiLiT', 'komtera'));
}

$sql = "delete from {$urunlerTable} WHERE id=?";
$stmt= $conn->prepare($sql);
$stmt->execute([$bb]);

?>
