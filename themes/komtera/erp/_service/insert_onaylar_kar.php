<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marka = isset($_POST['marka']) ? trim($_POST['marka']) : '';
    $seviye = isset($_POST['seviye']) ? (int)$_POST['seviye'] : null;
    $bayi_ch_kodu = isset($_POST['bayi_ch_kodu']) ? trim($_POST['bayi_ch_kodu']) : null;
    $onay1_oran = isset($_POST['onay1_oran']) ? floatval($_POST['onay1_oran']) : 0;
    $onay1_mail = isset($_POST['onay1_mail']) ? trim($_POST['onay1_mail']) : '';
    $onay2_oran = isset($_POST['onay2_oran']) ? floatval($_POST['onay2_oran']) : 0;
    $onay2_mail = isset($_POST['onay2_mail']) ? trim($_POST['onay2_mail']) : '';

    if (empty($marka)) {
        echo json_encode(['success' => false, 'message' => 'Marka zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_ayarlar_onaylar_kar');

    $sql = "INSERT INTO {$tableName} (marka, seviye, bayi_ch_kodu, onay1_oran, onay1_mail, onay2_oran, onay2_mail)
            VALUES (:marka, :seviye, :bayi_ch_kodu, :onay1_oran, :onay1_mail, :onay2_oran, :onay2_mail)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':marka', $marka, PDO::PARAM_STR);
        $stmt->bindParam(':seviye', $seviye, PDO::PARAM_INT);
        $stmt->bindParam(':bayi_ch_kodu', $bayi_ch_kodu, PDO::PARAM_STR);
        $stmt->bindParam(':onay1_oran', $onay1_oran);
        $stmt->bindParam(':onay1_mail', $onay1_mail, PDO::PARAM_STR);
        $stmt->bindParam(':onay2_oran', $onay2_oran);
        $stmt->bindParam(':onay2_mail', $onay2_mail, PDO::PARAM_STR);
        $stmt->execute();

        $newId = $conn->lastInsertId();

        echo json_encode(['success' => true, 'message' => 'Kayıt başarıyla eklendi', 'id' => $newId]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>
