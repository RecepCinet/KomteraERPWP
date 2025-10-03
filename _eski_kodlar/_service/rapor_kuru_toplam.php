<?php
error_reporting(E_ALL);
ini_set('display_erros', true);

include '../_conn.php';

$teklif_no=$_GET['teklif_no'];

$sql =<<<DATA
select f.VADE,f.PARA_BIRIMI,f.BAYI_CHKODU from aa_erp_kt_teklifler t
    LEFT JOIN aa_erp_kt_firsatlar f on f.FIRSAT_NO=t.X_FIRSAT_NO
    where t.TEKLIF_NO='$teklif_no'
DATA;
$stmt = $conn->query($sql);
$ham = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

//print_r($ham);

$para_birimi=$ham['PARA_BIRIMI'];
$bayi_chkodu=$ham['BAYI_CHKODU'];

$vade=$ham['VADE'];

$limit=0;
$bakiye=0;

$sql =<<<DATA
select LIMIT,BAKIYE from aaa_erp_kt_bayiler where RISK_DOVIZ_TURU='RAPORLAMA_DOVIZI' AND CH_KODU='$bayi_chkodu'
DATA;
$stmt = $conn->query($sql);
$bayi_data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($bayi_data) {
    $limit=$bayi_data['LIMIT'];
    $bakiye=$bayi_data['BAKIYE'];
}

//echo "limit : $limit" . PHP_EOL;
//echo "bakiye: $bakiye" . PHP_EOL;
//echo "vade: $vade" . PHP_EOL;

if ($vade=="PEŞİN" || $vade=="KKART" || $limit=="0") {
    die("0");
}

$sql =<<<DATA
select top 1 USD,EUR from aa_erp_kur k order by tarih desc
DATA;
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$usd=$data['USD'];
$eur=$data['EUR'];

//echo $usd . PHP_EOL;
//echo $eur . PHP_EOL;

$sql =<<<DATA
select sum(ADET*B_SATIS_FIYATI) as Toplam from aa_erp_kt_teklifler_urunler tu
    where tu.X_TEKLIF_NO='$teklif_no'
DATA;
$stmt = $conn->query($sql);
$ham_toplam = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['Toplam'];

//echo "toplam: $ham_toplam" . PHP_EOL;

$toplam=0;
if ($para_birimi=="TRY") {
    $toplam=$ham_toplam/$usd;
}
if ($para_birimi=="EUR") {
    $toplam=$ham_toplam*($eur/$usd);
}
if ($para_birimi=="USD") {
    $toplam=$ham_toplam;
}
//$toplam=70000;
if ($toplam+$bakiye>$limit) {
    echo "1";
} else {
    echo "0";
}

?>