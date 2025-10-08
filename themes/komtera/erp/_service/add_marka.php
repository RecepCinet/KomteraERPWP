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

    // Marka zaten var mı kontrol et
    $checkSql = "SELECT COUNT(*) FROM {$tableName} WHERE marka = :marka";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([':marka' => $marka]);
    $exists = $checkStmt->fetchColumn() > 0;

    if ($exists) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Bu marka zaten mevcut']);
        exit;
    }

    // Boş bir kayıt ekle (sadece marka ile) - Excel'den Al ile doldurulacak
    $insertSql = "INSERT INTO {$tableName} (marka, sku, urunAciklama) VALUES (:marka, :sku, :aciklama)";
    $insertStmt = $conn->prepare($insertSql);
    $placeholderSku = '_NEW_MARKA_' . time();
    $insertStmt->execute([
        ':marka' => $marka,
        ':sku' => $placeholderSku,
        ':aciklama' => 'Yeni marka - Excel\'den Al ile fiyat listesi yükleyiniz'
    ]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Marka başarıyla eklendi. Excel\'den Al ile fiyat listesini yükleyebilirsiniz.',
        'marka' => $marka
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}
