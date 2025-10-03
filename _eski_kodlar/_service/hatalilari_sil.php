<?php

include '../_conn.php';

$url = "delete from aa_erp_kt_fatura_i where r_result='error'";
$stmt = $conn->prepare($url);
$stmt->execute();

?>