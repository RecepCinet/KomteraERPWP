<?php
include '_func.php';
include '../_conn.php';
$id=$_GET['id'];
$sql="select _faturami from aa_erp_kt_fatura_i where id='$id'";
echo $sql . "\n";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['_faturami'];
$ek = " _status_i='0' " ;
if ($data==1) {
    // fatura:
    $ek = " _status_f='0' " ;
}
$sql="update aa_erp_kt_fatura_i set $ek,r_result=null,r_response=null where id='$id'";
echo $sql . "\n";
$up_st = $conn->prepare($sql);
$up_st->execute();
?>