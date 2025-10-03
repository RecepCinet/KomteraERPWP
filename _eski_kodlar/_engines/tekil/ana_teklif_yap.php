<?php

$firsat_no = $_GET['firsat_no'];
$teklif_no = $_GET['teklif_no'];
$stmt = $conn->prepare("update aa_erp_kt_teklifler set TEKLIF_TIPI=
CASE 
WHEN TEKLIF_NO=:teklif_no THEN 1 
ELSE 0
END
where X_FIRSAT_NO=:firsat_no");
$stmt->execute(['teklif_no' => "$teklif_no", 'firsat_no' => "$firsat_no"]);
?>
