<?php
error_reporting(E_ALL);
ini_set("display_errors", false);

include '../_conn.php';

$sku=$_GET['sku'];

$stmt = $conn->prepare("select listeFiyatiUpLift from aa_erp_kt_fiyat_listesi FL where SKU=:sku");
$stmt->execute(['sku' => $sku]); 
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

echo $gelen['listeFiyatiUpLift'];

?>
