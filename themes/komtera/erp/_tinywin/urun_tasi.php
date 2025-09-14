<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

include '../../_conn.php';

$yon = $_GET['yon'];
$sira = $_GET['sira'];
$id = $_GET['id'];
$teklif_no = $_GET['teklif_no'];

if ($yon === '0') {
    // Yukari tasimak!
    $sql = "select top 1 id,SIRA from aa_erp_kt_teklifler_urunler where X_TEKLIF_NO='$teklif_no' and SIRA<$sira order by SIRA desc";
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sonuc = "";
    if ($data) {
        $sonuc = $data[0];
        $u_sira = $sonuc['SIRA'];
        $u_id = $sonuc['id'];
    }
    if ($sonuc != "") {
        // yukarisi var tasi!
        $sqlupdate = "update aa_erp_kt_teklifler_urunler set SIRA='$u_sira' WHERE X_TEKLIF_NO='$teklif_no' and id='$id' ";
        $stmt = $conn->prepare($sqlupdate);
        $stmt->execute();
        if ($stmt == false) {
            throw new Exception(print_r($stmt->errorInfo(), 1) . PHP_EOL . $sqlupdate);
        }
        $sqlupdate = "update aa_erp_kt_teklifler_urunler set SIRA='$sira' WHERE X_TEKLIF_NO='$teklif_no' and id='$u_id' ";
        $stmt = $conn->prepare($sqlupdate);
        $stmt->execute();
        if ($stmt == false) {
            throw new Exception(print_r($stmt->errorInfo(), 1) . PHP_EOL . $sqlupdate);
        }
    }
}

if ($yon === '1') {
    // Yukari tasimak!
    $sql = "select top 1 id,SIRA from aa_erp_kt_teklifler_urunler where X_TEKLIF_NO='$teklif_no' and SIRA>$sira order by SIRA";
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sonuc = "";
    if ($data) {
        $sonuc = $data[0];
        $u_sira = $sonuc['SIRA'];
        $u_id = $sonuc['id'];
    }
    if ($sonuc != "") {
        // yukarisi var tasi!
        $sqlupdate = "update aa_erp_kt_teklifler_urunler set SIRA='$u_sira' WHERE X_TEKLIF_NO='$teklif_no' and id='$id' ";
        $stmt = $conn->prepare($sqlupdate);
        $stmt->execute();
        if ($stmt == false) {
            throw new Exception(print_r($stmt->errorInfo(), 1) . PHP_EOL . $sqlupdate);
        }
        $sqlupdate = "update aa_erp_kt_teklifler_urunler set SIRA='$sira' WHERE X_TEKLIF_NO='$teklif_no' and id='$u_id' ";
        $stmt = $conn->prepare($sqlupdate);
        $stmt->execute();
        if ($stmt == false) {
            throw new Exception(print_r($stmt->errorInfo(), 1) . PHP_EOL . $sqlupdate);
        }
    }
}
?>
