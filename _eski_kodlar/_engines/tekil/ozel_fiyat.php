<?php
$teklif_no = $_GET['teklif_no'];
$stmt = $conn->prepare("select case when sum(B_MALIYET)>0 THEN 1 else 0 END AS m
from aa_erp_kt_teklifler_urunler tu where tu.X_TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["m"];
echo $gelen

?>
