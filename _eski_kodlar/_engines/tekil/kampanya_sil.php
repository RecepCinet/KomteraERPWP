<?php

$id = $_GET['id'];
$stmt = $conn->prepare("delete from aa_erp_kt_kampanyalar where id=:id");
$stmt->execute(['id' => $id]);
//$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
//$json= json_encode($gelen[0]);
//echo $gelen['val'];
?>
