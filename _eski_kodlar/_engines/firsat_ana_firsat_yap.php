<?php

error_reporting(0);
ini_set("display_errors", false);

include '../_conn.php';
$firsat_no=$_GET['firsat_no'];
$bagli_no=$_GET['bagli_no'];


$query = "UPDATE aa_erp_kt_firsatlar set FIRSAT_ANA=null WHERE BAGLI_FIRSAT_NO='$bagli_no'";
$stmt = $conn->prepare($query);
$stmt->execute();

$query = "UPDATE aa_erp_kt_firsatlar set FIRSAT_ANA='1' WHERE FIRSAT_NO='$firsat_no'";
$stmt = $conn->prepare($query);
$stmt->execute();



?>