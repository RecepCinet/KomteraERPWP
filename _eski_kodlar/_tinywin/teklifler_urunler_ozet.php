<?php
error_reporting(E_ALL);
ini_set("display_errors", false);

include '../_conn.php';

$tn=$_GET['tn'];

$stmt = $conn->prepare("select * from aa_erp_kt_teklifler_urunler where X_TEKLIF_NO=:tn");
$stmt->execute(['tn' => $tn]); 
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

//print_r($gelen);

echo "SKU" . chr(9) . "Adet" . chr(9) . "Tutar" . chr(10);

for ($t=0;$t<count($gelen);$t++) {
    $satir=$gelen[$t];

    $sku=$satir['SKU'];
    $sf=$satir['B_SATIS_FIYATI'];
    $a=$satir['ADET'];
    $to=($sf*$a);

      echo $sku . chr(9) . number_format($a,0,',','.') . chr(9) . number_format($to,0,',','.') . chr(10);
}

?>
