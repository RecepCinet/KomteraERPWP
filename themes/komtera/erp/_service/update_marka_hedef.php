<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $marka = isset($_POST['marka']) ? trim($_POST['marka']) : '';
    $q1 = isset($_POST['q1']) ? floatval($_POST['q1']) : 0;
    $q2 = isset($_POST['q2']) ? floatval($_POST['q2']) : 0;
    $q3 = isset($_POST['q3']) ? floatval($_POST['q3']) : 0;
    $q4 = isset($_POST['q4']) ? floatval($_POST['q4']) : 0;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz ID']);
        exit;
    }

    if (empty($marka)) {
        echo json_encode(['success' => false, 'message' => 'Marka zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_mt_hedefler');

    $sql = "UPDATE {$tableName} SET
        marka = :marka,
        q1 = :q1,
        q2 = :q2,
        q3 = :q3,
        q4 = :q4
    WHERE id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':marka', $marka, PDO::PARAM_STR);
        $stmt->bindParam(':q1', $q1);
        $stmt->bindParam(':q2', $q2);
        $stmt->bindParam(':q3', $q3);
        $stmt->bindParam(':q4', $q4);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Hedef başarıyla güncellendi']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>
