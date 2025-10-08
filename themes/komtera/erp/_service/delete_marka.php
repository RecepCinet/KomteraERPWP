<?php
session_start();
include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Sadece POST metodu kabul edilir']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['marka']) || empty(trim($data['marka']))) {
    echo json_encode(['success' => false, 'message' => 'Marka adı gerekli']);
    exit;
}

$marka = trim($data['marka']);

try {
    $conn->beginTransaction();

    $tableName = getTableName('aa_erp_kt_fiyat_listesi');

    // Önce kaç kayıt silineceğini öğren
    $countSql = "SELECT COUNT(*) FROM {$tableName} WHERE marka = :marka";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute([':marka' => $marka]);
    $kayitSayisi = $countStmt->fetchColumn();

    if ($kayitSayisi == 0) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Bu marka için kayıt bulunamadı']);
        exit;
    }

    // Markaya ait tüm kayıtları sil
    $deleteSql = "DELETE FROM {$tableName} WHERE marka = :marka";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->execute([':marka' => $marka]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Marka başarıyla silindi',
        'marka' => $marka,
        'deleted' => $kayitSayisi
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}
