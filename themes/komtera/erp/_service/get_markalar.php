<?php
session_start();
include '../_conn.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT DISTINCT marka FROM fiyat_listesi WHERE marka IS NOT NULL AND marka != '' ORDER BY marka";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $markalar = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['success' => true, 'markalar' => $markalar]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>