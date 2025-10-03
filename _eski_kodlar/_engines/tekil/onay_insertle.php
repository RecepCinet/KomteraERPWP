<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

$teklif_no=$_GET['teklif_no'];

$stmt = $conn->prepare("select X_FIRSAT_NO from aa_erp_kt_teklifler f where f.TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$firsat_no=$gelen['X_FIRSAT_NO'];

//print_r($gelen);

$json= file_get_contents("http://127.0.0.1/_engines/tekil_getir.php?cmd=kar_oranlari&firsat_no=$firsat_no");

$gelen= json_decode($json,true);

print_r($gelen);


?>
