<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$sql = "SELECT
            k.id,
            k.marka,
            k.seviye,
            k.bayi_ch_kodu,
            b.CH_UNVANI as bayi_unvani,
            k.onay1_oran,
            k.onay1_mail,
            k.onay2_oran,
            k.onay2_mail
        FROM " . getTableName('aa_erp_kt_ayarlar_onaylar_kar') . " k
        LEFT JOIN " . getTableName('aaa_erp_kt_bayiler') . " b ON k.bayi_ch_kodu = b.CH_KODU
        ORDER BY k.marka, k.seviye, k.bayi_ch_kodu";

try {
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
    exit;
}

$response = [
    'data' => $data
];

if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . json_encode($response) . ')';
} else {
    echo json_encode($response);
}
?>
