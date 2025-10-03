<?php
error_reporting(E_ERROR);
ini_set("display_errors", true);

$siparis_no=$_GET['siparis_no'];

include '../_conn.php';

$sqlinsert = "DELETE FROM aa_erp_kt_fatura_i where siparisNO='$siparis_no'";
$stmt = $conn->prepare($sqlinsert);
$result = $stmt->execute();  

echo "$siparis_no Yeni Fatura tablosundan silindi<br /><br />";

$sqlinsert = "DELETE FROM aa_erp_kt_fatura_urunler_i where _x_siparisNO='$siparis_no'";
$stmt = $conn->prepare($sqlinsert);
$result = $stmt->execute();  

echo "$siparis_no Yeni Fatura Urunler tablosundan silindi<br /><br />";

?>