<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableName = getTableName('aa_erp_kt_tl_fatura_marka');

    $sql = "DELETE FROM {$tableName}";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $affectedRows = $stmt->rowCount();

        echo json_encode([
            'success' => true,
            'message' => 'Tüm markalar başarıyla çıkarıldı',
            'affected_rows' => $affectedRows
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>
