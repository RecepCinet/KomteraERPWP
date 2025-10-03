<?php

error_reporting(E_ALL);
ini_set("display_errors", true);
$u = "recep.cinet";
$s = "252w5";

try {
    $conn2 = new PDO("odbc:KOMTERA2021_64", "Admin", "KlyA2gw1");
    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "!";
    echo "--- FileMaker Data Baglanti Sorunu!---<br />" . PHP_EOL;
    die(print_r($e->getMessage()));
}


$sqlstring = "select * from TF_USERS where kullanici='$u' and sifre='$s'";

$stmt = $conn2->prepare($sqlstring);
$stmt->execute();
$sql = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($sql) {
    echo "var";
}

die
?>


