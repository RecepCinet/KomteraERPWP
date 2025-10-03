<?php
//error_reporting(E_ALL);
//ini_set("display_errors", true);

$siparis_no = $_GET['siparis_no'];
$stmt = $conn->prepare("SELECT SONUC from ARYD_FIS_AKTARIM where [NO]=:siparis_no");
$stmt->execute(['siparis_no' => $siparis_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['SONUC'];
echo $gelen;

?>
