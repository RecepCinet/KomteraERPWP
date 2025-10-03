<?php
error_reporting(0);
ini_set("display_errors", 0);
include '../_conn.php';
$sku = $_GET['sku'];
$teklif_no = $_GET['teklif_no'];
$bayi_seviye=$_GET['bayi_seviye_kod'];
$komtera=$_GET['komtera'];

if ($bayi_seviye=="") {
    $bayi_seviye=1;
}
$adet=$_GET['adet'];
$ozel_maliyet=$_GET['ozel_maliyet'];
$maliyet=$_GET['maliyet'];
$omaliyet=$_GET['maliyet'];

$karlilik=$_GET['karlilik'] * 100;
if ($ozel_maliyet!="") {
    $maliyet=$ozel_maliyet;
}
$iskonto=$_GET['iskonto'];
$satis_fiyati=$_GET['satis_fiyati'];
$sql = "select *,(select SERI_LOT from aaa_erp_kt_stoklar where SKU='$sku' and DEPO_KODU='0'
) as track_type from aa_erp_kt_fiyat_listesi where sku='$sku'
";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cvp=$data[0];
foreach ($cvp as $key => $value) {
    ${$key}=$value;
}
$b_maliyet = $listeFiyati * ( 1 - ( ( ${"a_iskonto" . $bayi_seviye} ) / 100 ) );
//$toplam_liste_fiyati = $listeFiyati * $adet;
//$toplam_maliyet = $b_maliyet * $adet;
//$toplam_satis = $net_satis * $adet;
//$toplam_ozel_maliyet = $ozel_maliyet * $adet;
//$karlilik = ( 1 - ( $b_maliyet / $satis_fiyati ) ) * 100;
//if ($ozel_maliyet!=="") {
//    $karlilik =  (1 - ($toplam_ozel_maliyet / $toplam_satis)) * 100;
//}
$sira=1;
$sql_sira="select top 1 CASE WHEN SIRA is null THEN 1 ELSE SIRA+1 END as SIRA
from aa_erp_kt_teklifler_urunler
where x_TEKLIF_NO='$teklif_no' order by SIRA DESC  
";
$stmt2 = $conn->query($sql_sira);
$data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$cvp2=$data2[0];
$temp_sira=$cvp2['SIRA'];
if ($temp_sira>0) {
    $sira=$temp_sira;
}
$sqlinsert=<<<SQLS
INSERT INTO LKS.dbo.aa_erp_kt_teklifler_urunler
    (X_TEKLIF_NO,SKU,ACIKLAMA,TIP,SURE,ADET, B_LISTE_FIYATI,B_MALIYET, ISKONTO, B_SATIS_FIYATI, KARLILIK, TRACK_TYPE, SIRA)
        VALUES
            ('$teklif_no','$sku','$urunAciklama','$tur','$lisansSuresi','$adet','$listeFiyatiUpLift','$maliyet','$iskonto','$satis_fiyati','$karlilik','$track_type','$sira' )
SQLS;
try {
    $stmt = $conn->prepare($sqlinsert);
    $result = $stmt->execute();
    echo '0';
} catch (PDOException $e) {
    echo '1';
    BotMesaj("Teklif no: " . $teklif_no . "\n" . $e->getMessage() . "\n" . $sqlinsert );
}
?>
