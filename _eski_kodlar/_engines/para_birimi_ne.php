<?php
error_reporting(0);
ini_set("display_errors", false);

include '../_conn.php';

try {
    $sql_string="SELECT marka m,paraBirimi pb FROM LKS.dbo.aa_erp_kt_fiyat_listesi fl where paraBirimi is not null and fl.marka<>'marka' and fl.marka=:marka group by marka,paraBirimi order by marka";
    $stmt = $conn->prepare($sql_string);
    $stmt->execute(['marka' => $_GET['marka']]);
    $gelen = $stmt->fetch()['pb'];
    echo $gelen;
} catch (PDOException $e) {
    $cfn = basename($_SERVER['PHP_SELF']);
    BotMesaj("Marka: " . $_GET['marka'] . "\n" . $e->getMessage() . "\n" . $sql_string . "\n" . $_GET['user']);
    die("NOK|Sorun Teknik ekibe aktarilmistir!");
}