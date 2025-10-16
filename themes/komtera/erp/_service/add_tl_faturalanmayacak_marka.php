<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marka = isset($_POST['marka']) ? trim($_POST['marka']) : '';

    if (empty($marka)) {
        echo json_encode(['success' => false, 'message' => 'Marka adı zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_tl_fatura_marka');

    $sql = "INSERT INTO {$tableName} (marka) VALUES (:marka)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':marka', $marka, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Marka başarıyla eklendi']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Bu marka zaten listede mevcut']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>
