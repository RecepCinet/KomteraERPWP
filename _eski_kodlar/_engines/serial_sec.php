<?php

error_reporting(0);
ini_set('display_errors', false);

include '../_conn.php';

$siparis_no = $_GET['siparis_no'];
$serial = $_GET['serial'];
$id = $_GET['id'];

$stmt = $conn->prepare("update aa_erp_kt_siparisler_urunler set LISANS=:lisans WHERE id=:id ");
$stmt->execute(['lisans' => $serial,'id' => $id]);

?>