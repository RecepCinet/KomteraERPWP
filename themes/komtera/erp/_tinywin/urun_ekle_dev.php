<?php
error_reporting(0);
ini_set("display_errors", false);
include '../../_conn.php';
$sku = $_GET['sku'];
$teklif_no = $_GET['teklif_no'];
$f_adet = $_GET['adet'];
$bayi_seviye=$_GET['bayi_seviye_kod'];

$sql = "select
CASE WHEN
REGISTER is null THEN ''
ELSE
'_r'
END AS REGISTER,
MARKA
from " . getTableName('aa_erp_kt_firsatlar') . " f where FIRSAT_NO=(select X_FIRSAT_NO from " . getTableName('aa_erp_kt_teklifler') . " t WHERE TEKLIF_NO='$teklif_no')";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$register=$data['REGISTER'];
$marka=$data['MARKA'];
if ($bayi_seviye=="") {
    $bayi_seviye=1;
}
if ($marka=="SECHARD") {
    $bayi_seviye=1;
}
$adet=$_GET['adet'];

//$karlilik=$_GET['karlilik'] * 100;
if ($ozel_maliyet!="") {
    $maliyet=$ozel_maliyet;
}
$sql = "select *,(select SERI_LOT from " . getTableName('aaa_erp_kt_stoklar') . " where SKU='$sku' and DEPO_KODU='0'
) as track_type from " . getTableName('aa_erp_kt_fiyat_listesi') . " where marka='$marka' AND sku='$sku'
";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    die(__('Hata', 'komtera') . "|" . __('SKU Bulunamadı!', 'komtera'));    
}
$cvp=$data[0];

foreach ($cvp as $key => $value) {
    ${$key}=$value;
}
$o_maliyet   = $listeFiyati * ( 1 - ( ${"a_iskonto" . $bayi_seviye . $register} / 100 ) ) ;
$h_satis = $listeFiyatiUpLift * ( 1 - ( ${"s_iskonto" . $bayi_seviye . $register} / 100 ) ) ;
$sira=1;
$sql_sira="select top 1 CASE WHEN SIRA is null THEN 1 ELSE SIRA+1 END as SIRA,
    (select top 1 KILIT from " . getTableName('aa_erp_kt_teklifler') . " where TEKLIF_NO='$teklif_no') as FN,
    (select sum(ADET*B_SATIS_FIYATI) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.X_TEKLIF_NO ='$teklif_no') AS TT,
    (select sum(ADET*O_MALIYET) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.X_TEKLIF_NO ='$teklif_no') AS MM
from " . getTableName('aa_erp_kt_teklifler_urunler') . "
where x_TEKLIF_NO='$teklif_no' order by SIRA DESC
";
$stmt2 = $conn->query($sql_sira);
$data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$cvp2=$data2[0];

$tt=$cvp2['TT'];
$mm=$cvp2['MM'];

if ($marka=="SECHARD") {
// tekrar toplam aliyoruz urunlerin %20 sini yada %10 unu hesaplayabilmek icin:
$sql_sira="select top 1 CASE WHEN SIRA is null THEN 1 ELSE SIRA+1 END as SIRA,
    (select top 1 KILIT from " . getTableName('aa_erp_kt_teklifler') . " where TEKLIF_NO='$teklif_no') as FN,
    (select sum(ADET*B_SATIS_FIYATI) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.TIP='Licence' and tu.X_TEKLIF_NO ='$teklif_no') AS TT,
    (select sum(ADET*O_MALIYET) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.TIP='Licence' and tu.X_TEKLIF_NO ='$teklif_no') AS MM
from " . getTableName('aa_erp_kt_teklifler_urunler') . "
where x_TEKLIF_NO='$teklif_no' and TIP='Licence' order by SIRA DESC
";
$stmt2 = $conn->query($sql_sira);
$data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$cvp2=$data2[0];

$ttsl=$cvp2['TT'];
$mmsl=$cvp2['MM'];
}



if ($cvp2['FN']==="1") {
    die(__('Hata', 'komtera') . "," . __('KiLiT', 'komtera'));
}
$temp_sira=$cvp2['SIRA'];
if ($temp_sira>0) {
    $sira=$temp_sira;
}
if ($listeFiyatiUpLift==="") {
    $listeFiyatiUpLift=0.1;
}
$h_iskonto = ( ( $listeFiyatiUpLift - $h_satis ) / $listeFiyatiUpLift ) * 100;
if ($lisansSuresi=="") {
    $lisansSuresi="0";
}
$b_maliyet=0;
$vm=$_GET['hazir_fiyat'];
if ($vm!="") {
    $b_maliyet=$vm;
}
if (is_nan($h_iskonto)) {
    $h_iskonto='';
}
if (is_nan($o_maliyet)) {
    $o_maliyet='';
}
if (is_nan($h_satis)) {
    $h_satis='';
}

$urunAciklama=str_replace("'","",$urunAciklama);


if ($marka=="SECHARD") {
    include 'urun_ekle_sechard_dev.php';
}










$sqlinsert = "INSERT INTO LKS.dbo." . getTableName('aa_erp_kt_teklifler_urunler') . "
    (slot,X_TEKLIF_NO,SKU,ACIKLAMA,TIP,SURE,ADET, B_LISTE_FIYATI,B_MALIYET,O_MALIYET, ISKONTO, B_SATIS_FIYATI, SIRA)
    VALUES
    ('$track_type','$teklif_no','$sku','$urunAciklama','$tur','$lisansSuresi','$f_adet','$listeFiyatiUpLift','$b_maliyet','$o_maliyet','$h_iskonto','$h_satis','$sira' )";
try {
    $stmt = $conn->prepare($sqlinsert);
    $result = $stmt->execute();
    echo '0';
} catch (PDOException $e) {
    echo '1';
    BotMesaj("Teklif no: " . $teklif_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
}
?>
