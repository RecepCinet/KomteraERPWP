<?php

error_reporting(E_ALL);
ini_set("display_errors",true);
include "../_conn.php";

$marka=$_GET['marka'];
$bayi=$_GET['bayi'];
$mt1=$_GET['mt1'];
$mt2=$_GET['mt2'];

$markaeki="";
if ($marka!="") {
    $markaeki="and MARKA='$marka'";
}

$bayieki="";
if ($bayi!="") {
    $bayieki="and BAYI_CHKODU='$bayi'";
}

$sqlupdate = "update aa_erp_kt_firsatlar set MUSTERI_TEMSILCISI='$mt2' where DURUM=0 AND MUSTERI_TEMSILCISI = '$mt1' $markaeki $bayieki";
try {
    $stmt = $conn->prepare($sqlupdate);
    $result = $stmt->execute();
    echo "OK";
} catch (PDOException $e) {
    BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user'] );
    die("NOK|Sorun Teknik ekibe aktarilmistir!");
}

?>
