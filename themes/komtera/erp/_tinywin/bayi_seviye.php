<?php

// select SEVIYE 0 aa_erp_kt_bayiler_seviye bs where MARKA='' AND CH_KODU=''
error_reporting(0);
ini_set('display_errors',false);

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$marka  = $_GET['marka'];
$chkodu = $_GET['chkodu'];

$tableName = getTableName('aa_erp_kt_bayiler_markaseviyeleri');
$string="select CASE
WHEN bs.seviye = '1' THEN 'AUTHORIZED'
WHEN bs.seviye = '2' THEN 'SILVER'
WHEN bs.seviye = '3' THEN 'GOLD'
WHEN bs.seviye = '4' THEN 'PLATINUM'
ELSE ''
END AS seviye from {$tableName} bs where marka='$marka' AND CH_KODU='$chkodu'";

$stmt = $conn->query($string);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

echo $data['seviye'];

?>
