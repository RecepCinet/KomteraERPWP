<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

include '../_conn.php';

$teklif_no=$_GET['teklif_no'];
$kim=$_GET['kim'];

$string="UPDATE aa_erp_kt_teklifler SET yenileme_log=:kim WHERE TEKLIF_NO=:teklif_no";
$stmt = $conn->prepare($string);
if ($stmt->execute(['kim' => $kim, 'teklif_no' => $teklif_no])) {
    echo "Başarıyla güncellendi.";
} else {
    print_r($stmt->errorInfo());
}

?>
