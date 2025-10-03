<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

include '../_conn.php';

$siparis_no=$_GET['siparis_no'];

$stmt = $conn->prepare("select top 1 (select top 1 sum(SEC) from aa_erp_kt_siparisler_urunler u where u.X_SIPARIS_NO=s.SIPARIS_NO ) as SECILI,*
from aa_erp_kt_siparisler s
WHERE s.SIPARIS_NO=:siparis_no ORDER BY PARCA desc
");
$stmt->execute(['siparis_no' => $siparis_no]);
$d1 = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

print_r($d1);



$parca=(int)$d1['PARCA'];
$teklif_no=$d1['X_TEKLIF_NO'];
$x_firsat_no=$d1['X_FIRSAT_NO'];
$mussipno=$d1['MUSTERI_SIPARIS_NO'];
$ozelkur=$d1['OZEL_KUR'];

$parca++;

$yeni_siparis_no=$teklif_no . "-" . $parca;

$string="insert into aa_erp_kt_siparisler (SIPARIS_NO,PARCA,SIPARIS_DURUM,SIPARIS_DURUM_ALT,X_TEKLIF_NO,X_FIRSAT_NO,MUSTERI_SIPARIS_NO,OZEL_KUR)
values ('$yeni_siparis_no','$parca','0','0','$teklif_no','$x_firsat_no','$mussipno','$ozelkur')";
$sqlinsert = $string;
$stmt = $conn->prepare($sqlinsert);
$stmt->execute();

$string="INSERT INTO LKS.dbo.aa_erp_kt_siparisler_urunler
(X_SIPARIS_NO, SIRA, SKU, ACIKLAMA, TIP, SURE, ADET, BIRIM_FIYAT, LISANS, SERIAL, SEC, SEC_ADET)
(select '$yeni_siparis_no', SIRA, SKU, ACIKLAMA, TIP, SURE, SEC_ADET, BIRIM_FIYAT, LISANS, SERIAL, null, null from aa_erp_kt_siparisler_urunler WHERE X_SIPARIS_NO='$siparis_no' AND SEC='1')";

$stmt = $conn->prepare($string);
$stmt->execute();

$string="DELETE from LKS.dbo.aa_erp_kt_siparisler_urunler where ADET=SEC_ADET and X_SIPARIS_NO='$siparis_no'";
$stmt = $conn->prepare($string);
$stmt->execute();

$string="UPDATE aa_erp_kt_siparisler_urunler SET ADET=ADET-SEC_ADET,SEC=null,SEC_ADET=null WHERE sec=1";
$stmt = $conn->prepare($string);
$stmt->execute();

?>
