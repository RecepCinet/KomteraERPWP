<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT MARKA, COUNT(*) as KAYIT_SAYISI FROM " . getTableName('aa_erp_kt_fiyat_listesi') . " WHERE MARKA IS NOT NULL AND MARKA != '' GROUP BY MARKA ORDER BY MARKA";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $markalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'markalar' => $markalar]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>