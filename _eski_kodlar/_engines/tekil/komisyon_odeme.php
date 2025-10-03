<?php
error_reporting(E_ALL);
ini_set("display_errors",true);

$k = $_GET['aciklama'];
$id = $_GET['id'];
$tem = $_GET['tem'];

if ($tem=="1") {
    $stmt = $conn->prepare("update aa_erp_kt_teklifler set KOMISYON_ODENDI=null,KOMISYON_ODENDI_ACIKLAMA=null where id=:i");
    $stmt->execute(['i' => $id]);
} else {
    $stmt = $conn->prepare("update aa_erp_kt_teklifler set KOMISYON_ODENDI='1',KOMISYON_ODENDI_ACIKLAMA=:k where id=:i");
    $stmt->execute(['k' => $k,'i' => $id]);
}
?>
