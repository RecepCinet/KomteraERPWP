<?php
error_reporting(0);
ini_set("display_errors", false);

//select SIPARIS_NO from aa_erp_kt_siparisler s where s.SIPARIS_DURUM<>2 AND s.X_TEKLIF_NO = 'T303567'
$teklif_no = $_GET['teklif_no'];

$stmt = $conn->prepare("select SIPARIS_NO from aa_erp_kt_siparisler s where s.X_TEKLIF_NO =:teklif_no ");
$stmt->execute(['teklif_no' => $teklif_no]);
$siparis_no = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['SIPARIS_NO'];

if (!$siparis_no) {
    die("NOK|Sipariş Bulunamadı!");
}

$stmt = $conn->prepare("select SIPARIS_NO from aa_erp_kt_siparisler s where s.SIPARIS_DURUM=2 AND s.X_TEKLIF_NO =:teklif_no ");
$stmt->execute(['teklif_no' => $teklif_no]);
$siparis_no = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['SIPARIS_NO'];

if ($siparis_no) {
    die("NOK|Siparişin bazı parçaları kapalı geri çevrilemez!");
}

$stmt = $conn->prepare("delete from aa_erp_kt_siparisler_urunler where X_SIPARIS_NO like '$teklif_no-%'");
$stmt->execute();

$stmt = $conn->prepare("delete from aa_erp_kt_siparisler where SIPARIS_NO like '$teklif_no-%'");
$stmt->execute();

$stmt = $conn->prepare("delete from ARYD_FIS_AKTARIM where [NO] like '$teklif_no-%'");
$stmt->execute();

die("OK");

//  select SIPARIS_NO from aa_erp_kt_siparisler s where s.SIPARIS_DURUM<>2 AND s.X_TEKLIF_NO = 'T303567'

//$stmt = $conn->prepare("update aa_erp_kt_teklifler set TEKLIF_TIPI=
//CASE 
//WHEN TEKLIF_NO=:teklif_no THEN 1 
//ELSE 0
//END
//where X_FIRSAT_NO=:firsat_no");
//$stmt->execute(['teklif_no' => "$teklif_no", 'firsat_no' => "$firsat_no"]);
//
//

?>
