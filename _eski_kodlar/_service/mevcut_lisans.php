<?php
include '../_conn.php';

$teklif_no = $_GET['teklif_no'];

$toplam=0;

$url = "select t.TEKLIF_NO , t.SATIS_TIPI from aa_erp_kt_teklifler t where TEKLIF_NO =:teklif_no AND t.SATIS_TIPI<>'0'";
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $teklif_no]);
$t = $stmt->fetchAll(PDO::FETCH_ASSOC);

$toplam = count($t);

if ($toplam>0) {
    die("1");
} else {
    die('0');
}

$url = "select tu.X_TEKLIF_NO , tu.SATIS_TIPI from aa_erp_kt_teklifler_urunler tu where tu.X_TEKLIF_NO=:teklif_no AND tu.SATIS_TIPI='1'";
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $teklif_no]);
$tu = $stmt->fetchAll(PDO::FETCH_ASSOC);

$toplam += count($tu);

echo $toplam;

?>