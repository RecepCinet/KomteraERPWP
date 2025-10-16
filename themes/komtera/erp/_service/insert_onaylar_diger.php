<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key_name = isset($_POST['key_name']) ? trim($_POST['key_name']) : '';
    $aciklama = isset($_POST['aciklama']) ? trim($_POST['aciklama']) : '';
    $kullanici = isset($_POST['kullanici']) ? trim($_POST['kullanici']) : '';

    if (empty($key_name)) {
        echo json_encode(['success' => false, 'message' => 'Key (Anahtar) zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_ayarlar_onaylar');

    $sql = "INSERT INTO {$tableName} (kural, aciklama, kim)
            VALUES (:key_name, :aciklama, :kullanici)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':key_name', $key_name, PDO::PARAM_STR);
        $stmt->bindParam(':aciklama', $aciklama, PDO::PARAM_STR);
        $stmt->bindParam(':kullanici', $kullanici, PDO::PARAM_STR);
        $stmt->execute();

        $newId = $conn->lastInsertId();

        echo json_encode(['success' => true, 'message' => 'Kayıt başarıyla eklendi', 'id' => $newId]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Bu key zaten mevcut']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>
