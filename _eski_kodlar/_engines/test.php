<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

include '../_conn.php';

$stmt = $conn->prepare("select * from aa_erp_kt_mcafee_sku_sure where SKU=:sku");
$stmt->execute(['sku' => $_GET['SKU']]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

if  ( count($gelen) === 1 ) {
    echo "izin";
}

?>
