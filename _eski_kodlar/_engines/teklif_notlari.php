<?php
error_reporting(0);
ini_set("display_errors", false);
include '../_conn_fm.php';

$teklif_no=$_GET['teklif_no'];
$string = "select * from TF_GLOBAL";
$stmt = $conn2->prepare($string);
$stmt->execute();
$teklif_notu = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["TEKLIF_NOTLARI"];
echo $teklif_notu;


?>
