<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';

if (empty($_GET['logo_user'])) {
    die('Missing parameter: logo_user');
}

$logo_user = $_GET['logo_user'];

$norm='ERP_SATIS_ANALIZ_20XX';
$yil=$_GET['yil'];

if ($yil=="2024") {
    $norm='ERP_SATIS_ANALIZ_319_20XX';
}

$sql = "SELECT * FROM $norm WHERE Satis_Temsilcisi = :logo_user AND Yil = :yil";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':logo_user', $logo_user, PDO::PARAM_STR);
$stmt->bindParam(':yil', $yil, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = json_encode(['data' => $data]);

if (!empty($_GET['callback'])) {
    echo $_GET['callback'] . "($response)";
} else {
    echo $response;
}
?>