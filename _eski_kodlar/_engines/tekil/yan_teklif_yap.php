<?php

$fn = $_GET['firsat_no'];
$stmt = $conn->prepare("update aa_erp_kt_teklifler set TEKLIF_TIPI='0' where X_FIRSAT_NO=:firsat_no");
$stmt->execute(['firsat_no' => $fn]);
//$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
//$json= json_encode($gelen[0]);
//echo $gelen['val'];
?>

