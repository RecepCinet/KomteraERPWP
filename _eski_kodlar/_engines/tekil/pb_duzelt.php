<?php
error_reporting(0);
ini_set("display_errors", false);

$firsat_no = $_GET['firsat_no'];
$stmt = $conn->prepare("select marka from aa_erp_kt_firsatlar where FIRSAT_NO=:firsat_no");
$stmt->execute(['firsat_no' => $firsat_no]);
$marka = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['marka'];
//echo $marka;

$stmt = $conn->prepare("select top 1 paraBirimi from aa_erp_kt_fiyat_listesi where MARKA=:marka");
$stmt->execute(['marka' => $marka]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['paraBirimi'];

IF ($gelen=='') {
    echo "NOK";
}
else{
    echo $gelen;
}

?>