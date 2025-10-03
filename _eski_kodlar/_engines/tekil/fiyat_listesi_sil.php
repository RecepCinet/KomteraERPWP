<?php

$marka = $_GET['marka'];
$stmt = $conn->prepare("DELETE FROM aa_erp_kt_fiyat_listesi WHERE MARKA=:marka");
$stmt->execute(['marka' => $marka]);

?>
