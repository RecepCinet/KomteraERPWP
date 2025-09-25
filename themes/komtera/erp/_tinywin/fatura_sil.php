<?php

error_reporting(0);
ini_set("display_errors", false);

include '../../_conn.php';

$siparis_no = $_GET['siparis_no'];

$url = "select count(ID) AS BU,
SUM(
CASE
WHEN SONUC='4' THEN 1
WHEN SONUC<>4 THEN 0
END) as BUNA
from ARYD_FIS_AKTARIM where [NO]=:siparis_no";
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$s = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

IF ($s['BU']===$s['BUNA']) {
    die (__('Hata', 'komtera') . '|' . __('Logoya Düzgün Aktarım Gerçekleştiği için silemsiniz!', 'komtera'));
}

IF ($s['BU']==="0") {
    die (__('Hata', 'komtera') . '|' . __('Tabloda Kayıt Bulunamadı!', 'komtera'));
}

$sqldel = "delete from LKS.dbo.ARYD_FIS_AKTARIM WHERE [NO]=:siparis_no";
  try {
$stmt = $conn->prepare($sqldel);
$result = $stmt->execute(['siparis_no' => $siparis_no]);
    echo __('Başarılı', 'komtera');
        } catch (PDOException $e) {
            BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqldel . "\n" . $_GET['user'] );
            die(__('Hata', 'komtera') . "|" . __('Sorun Teknik ekibe aktarılmıştır!', 'komtera'));
        }
?>
