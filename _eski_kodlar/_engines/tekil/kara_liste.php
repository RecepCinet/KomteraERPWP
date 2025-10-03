<?php

$ch_kodu=$_GET['ch_kodu'];

$stmt = $conn->prepare("select dikkat_listesi,kara_liste from aa_erp_kt_bayiler_kara_liste kl where kl.ch_kodu =:ch_kodu");
$stmt->execute(['ch_kodu' => $ch_kodu]);
$arr = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$dikkat_listesi=$arr['dikkat_listesi'];
$kara_liste=$arr['kara_liste'];

if ($dikkat_listesi==="1" || $kara_liste==="1") {
    $out="NOK|";
    if ($dikkat_listesi==="1") {
        $out .= "Bu Bayi Dikkat Listesinde!\n";
    }
    if ($kara_liste==="1") {
        $out .= "Bu Bayi Kara Listede!\n";
    }
    echo $out;
} else {
    echo "OK";
}
?>
