<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

$teklif_no = $_GET['teklif_no'];
$stmt = $conn->prepare("select 
sum(
case
when B_MALIYET*ADET>0 THEN B_MALIYET*ADET
else O_MALIYET*ADET END
) as m,
sum(B_SATIS_FIYATI*ADET) as s
from aa_erp_kt_teklifler_urunler tu where tu.X_TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$stmt = $conn->prepare("select
CASE
WHEN B_MALIYET>0 THEN ( ( B_SATIS_FIYATI - B_MALIYET ) / NULLIF(B_SATIS_FIYATI,0) ) * 100
ELSE ( ( B_SATIS_FIYATI - O_MALIYET ) / NULLIF(B_SATIS_FIYATI,0) ) * 100
END AS KARLILIK from aa_erp_kt_teklifler_urunler aektu where X_TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen2 = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$stmt = $conn->prepare("select TEKLIF_NO,t_maliyet as a,KOMISYON_F1 c,KOMISYON_F2 d,KOMTERA_HIZMET_BEDELI e,KOMISYON_F3 f,KOMISYON_H g,t_satis as s from aa_erp_kt_teklifler t where t.TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$teklif_no=$data['TEKLIF_NO'];

$stmt = $conn->prepare("select sum(B_SATIS_FIYATI) as b from aa_erp_kt_teklifler_urunler tu where tu.TIP='Komtera' AND tu.X_TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$B = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['b'];

$A=$data['a'];

$C=$data['c'];
$D=$data['d'];
$E=$data['e'];
$F=$data['f'];
$G=$data['g'];
$S=$data['s'];

$kar=$S-(($A)+$B+$C+$D+$E+$F);
$payda1 = $S - ($B + $C + $D + $E + $F);
if ($payda1 != 0) {
    $karlilik = ($kar / $payda1) * 100;
} else {
    $karlilik = 0; // Veya uygun bir varsayılan değer
}
$kom_kar=$S-($A)-$C-$D;
$payda2 = $S - $C - $D;
if ($payda2 != 0) {
    $kom_karlilik = $kom_kar / $payda2;
} else {
    $kom_karlilik = 0; // Veya uygun bir varsayılan değer
}

//$gelen['kar']=$kar;
$gelen['HKARLILIK']=$karlilik;

$json = json_encode($gelen);
echo $json;

?>